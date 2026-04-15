<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAnswer;
use App\Models\Lesson;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class CourseStudents extends Component
{
    use WithPagination;

    public Course $course;
    public string $search = '';
    public string $filterStatus = '';

    // Detail modal
    public bool $showDetailModal = false;
    public ?int $viewingStudentId = null;

    // Remove modal
    public bool $showRemoveModal = false;
    public ?int $removingStudentId = null;

    // Quiz answers modal
    public bool $showAnswersModal = false;
    public ?int $answersStudentId = null;
    public ?int $answersLessonId = null;

    // Essay grading
    public array $essayScores = [];   // [answer_id => points]
    public array $essayFeedback = []; // [answer_id => feedback text]

    public function mount(int $courseId): void
    {
        $this->course = Course::with(['modules.lessons', 'instructor'])->findOrFail($courseId);

        // Check ownership for instructors
        $user = auth()->user();
        if ($user->isInstructor() && $this->course->instructor_id !== $user->id) {
            abort(403);
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // ─── View Student Detail ──────────────────────────

    public function viewDetail(int $studentId): void
    {
        $this->viewingStudentId = $studentId;
        $this->answersLessonId = null;
        $this->showDetailModal = true;
    }

    // ─── View Quiz Answers ────────────────────────────

    public function viewAnswers(int $studentId, int $lessonId): void
    {
        $this->answersStudentId = $studentId;
        $this->answersLessonId = $lessonId;
        $this->essayScores = [];
        $this->essayFeedback = [];

        // Pre-fill existing essay scores and feedback
        $answers = QuizAnswer::where('user_id', $studentId)
            ->where('lesson_id', $lessonId)
            ->get();

        foreach ($answers as $answer) {
            $question = $answer->question;
            if ($question && $question->type === 'essay') {
                $this->essayScores[$answer->id] = $answer->points_earned;
                $this->essayFeedback[$answer->id] = $answer->essay_feedback ?? '';
            }
        }

        $this->showAnswersModal = true;
    }

    // ─── Grade Essay ─────────────────────────────────

    public function gradeEssay(int $answerId): void
    {
        $answer = QuizAnswer::with('question')->findOrFail($answerId);

        // Verify this answer belongs to the course
        $lessonIds = $this->course->lessons()->pluck('id');
        if (!$lessonIds->contains($answer->lesson_id)) {
            abort(403);
        }

        $maxPoints = $answer->question->points;
        $score = min(max(intval($this->essayScores[$answerId] ?? 0), 0), $maxPoints);
        $feedback = trim($this->essayFeedback[$answerId] ?? '');

        $answer->update([
            'points_earned'  => $score,
            'is_correct'     => $score >= ($maxPoints * 0.6),
            'essay_feedback'  => $feedback ?: null,
            'graded_by'      => auth()->id(),
            'graded_at'      => now(),
        ]);

        // Recalculate total quiz score for this student+lesson
        $allAnswers = QuizAnswer::where('user_id', $answer->user_id)
            ->where('lesson_id', $answer->lesson_id)
            ->get();

        $totalEarned = $allAnswers->sum('points_earned');
        $lesson = Lesson::with('quizQuestions')->find($answer->lesson_id);
        $totalPossible = $lesson->quizQuestions->sum('points');
        $percentage = $totalPossible > 0 ? ($totalEarned / $totalPossible) * 100 : 0;

        // Auto-complete lesson if quiz passes 60%
        if ($percentage >= 60) {
            LessonProgress::updateOrCreate(
                ['user_id' => $answer->user_id, 'lesson_id' => $answer->lesson_id],
                ['course_id' => $this->course->id, 'is_completed' => true, 'completed_at' => now()]
            );
            $this->course->recalculateProgressFor($answer->user_id);
        }

        session()->flash('success', 'Nilai esai berhasil disimpan.');
    }

    public function gradeAllEssays(): void
    {
        if (!$this->answersStudentId || !$this->answersLessonId) return;

        $answers = QuizAnswer::with('question')
            ->where('user_id', $this->answersStudentId)
            ->where('lesson_id', $this->answersLessonId)
            ->get();

        foreach ($answers as $answer) {
            if ($answer->question->type !== 'essay') continue;
            if (!isset($this->essayScores[$answer->id])) continue;

            $maxPoints = $answer->question->points;
            $score = min(max(intval($this->essayScores[$answer->id] ?? 0), 0), $maxPoints);
            $feedback = trim($this->essayFeedback[$answer->id] ?? '');

            $answer->update([
                'points_earned'  => $score,
                'is_correct'     => $score >= ($maxPoints * 0.6),
                'essay_feedback'  => $feedback ?: null,
                'graded_by'      => auth()->id(),
                'graded_at'      => now(),
            ]);
        }

        // Recalculate quiz score
        $allAnswers = QuizAnswer::where('user_id', $this->answersStudentId)
            ->where('lesson_id', $this->answersLessonId)
            ->get();

        $totalEarned = $allAnswers->sum('points_earned');
        $lesson = Lesson::with('quizQuestions')->find($this->answersLessonId);
        $totalPossible = $lesson->quizQuestions->sum('points');
        $percentage = $totalPossible > 0 ? ($totalEarned / $totalPossible) * 100 : 0;

        if ($percentage >= 60) {
            LessonProgress::updateOrCreate(
                ['user_id' => $this->answersStudentId, 'lesson_id' => $this->answersLessonId],
                ['course_id' => $this->course->id, 'is_completed' => true, 'completed_at' => now()]
            );
            $this->course->recalculateProgressFor($this->answersStudentId);
        }

        session()->flash('success', 'Semua nilai esai berhasil disimpan.');
    }

    // ─── Remove Student ───────────────────────────────

    public function confirmRemove(int $studentId): void
    {
        $this->removingStudentId = $studentId;
        $this->showRemoveModal = true;
    }

    public function removeStudent(): void
    {
        if (!$this->removingStudentId) return;

        // Delete enrollment
        Enrollment::where('user_id', $this->removingStudentId)
            ->where('course_id', $this->course->id)
            ->delete();

        // Delete lesson progress
        LessonProgress::where('user_id', $this->removingStudentId)
            ->where('course_id', $this->course->id)
            ->delete();

        // Delete quiz answers for this course
        $lessonIds = $this->course->lessons()->pluck('id');
        QuizAnswer::where('user_id', $this->removingStudentId)
            ->whereIn('lesson_id', $lessonIds)
            ->delete();

        $this->showRemoveModal = false;
        $this->removingStudentId = null;
        session()->flash('success', 'Siswa berhasil dikeluarkan dari kursus.');
    }

    public function render()
    {
        // Enrolled students
        $query = Enrollment::with('user')
            ->where('course_id', $this->course->id);

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('nis', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus === 'completed') {
            $query->where('status', 'completed');
        } elseif ($this->filterStatus === 'active') {
            $query->where('status', 'active');
        }

        $enrollments = $query->latest()->paginate(20);

        // Get all lessons (for progress view)
        $allLessons = $this->course->modules->flatMap->lessons;
        $quizLessons = $allLessons->where('type', 'quiz');

        // Stats
        $totalEnrolled = Enrollment::where('course_id', $this->course->id)->count();
        $completedCount = Enrollment::where('course_id', $this->course->id)->where('status', 'completed')->count();
        $avgProgress = Enrollment::where('course_id', $this->course->id)->avg('progress_percentage') ?? 0;

        // Pending essay grading count
        $lessonIds = $allLessons->pluck('id');
        $pendingEssayCount = QuizAnswer::whereIn('lesson_id', $lessonIds)
            ->whereHas('question', fn($q) => $q->where('type', 'essay'))
            ->whereNotNull('essay_answer')
            ->whereNull('graded_at')
            ->count();

        // Student detail data
        $viewingStudent = null;
        $studentProgress = collect();
        $studentQuizResults = collect();
        if ($this->viewingStudentId) {
            $viewingStudent = User::find($this->viewingStudentId);
            $studentProgress = LessonProgress::where('user_id', $this->viewingStudentId)
                ->where('course_id', $this->course->id)
                ->pluck('is_completed', 'lesson_id');

            // Quiz results per quiz lesson
            foreach ($quizLessons as $quiz) {
                $answers = QuizAnswer::where('user_id', $this->viewingStudentId)
                    ->where('lesson_id', $quiz->id)
                    ->get();
                if ($answers->isNotEmpty()) {
                    $essayCount = $quiz->quizQuestions->where('type', 'essay')->count();
                    $ungradedEssays = $answers->filter(fn($a) => $a->question?->type === 'essay' && $a->essay_answer && !$a->graded_at)->count();
                    $studentQuizResults[$quiz->id] = [
                        'lesson' => $quiz,
                        'total_points' => $quiz->quizQuestions->sum('points'),
                        'earned_points' => $answers->sum('points_earned'),
                        'correct_count' => $answers->where('is_correct', true)->count(),
                        'total_questions' => $quiz->quizQuestions->count(),
                        'essay_count' => $essayCount,
                        'ungraded_essays' => $ungradedEssays,
                    ];
                }
            }
        }

        // Quiz answers detail
        $quizAnswersDetail = collect();
        $answersLesson = null;
        $answersStudent = null;
        $hasEssayQuestions = false;
        if ($this->showAnswersModal && $this->answersStudentId && $this->answersLessonId) {
            $answersLesson = Lesson::with('quizQuestions.options')->find($this->answersLessonId);
            $answersStudent = User::find($this->answersStudentId);
            $quizAnswersDetail = QuizAnswer::with('grader')
                ->where('user_id', $this->answersStudentId)
                ->where('lesson_id', $this->answersLessonId)
                ->get()
                ->keyBy('quiz_question_id');
            $hasEssayQuestions = $answersLesson?->quizQuestions->contains('type', 'essay') ?? false;
        }

        return view('livewire.admin.course-students', compact(
            'enrollments', 'allLessons', 'quizLessons', 'totalEnrolled',
            'completedCount', 'avgProgress', 'pendingEssayCount',
            'viewingStudent', 'studentProgress',
            'studentQuizResults', 'quizAnswersDetail', 'answersLesson', 'answersStudent',
            'hasEssayQuestions'
        ))->layout('layouts.admin', ['title' => 'Siswa: ' . $this->course->title]);
    }
}
