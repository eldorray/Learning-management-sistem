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
        $this->showAnswersModal = true;
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
                    $studentQuizResults[$quiz->id] = [
                        'lesson' => $quiz,
                        'total_points' => $quiz->quizQuestions->sum('points'),
                        'earned_points' => $answers->sum('points_earned'),
                        'correct_count' => $answers->where('is_correct', true)->count(),
                        'total_questions' => $quiz->quizQuestions->count(),
                    ];
                }
            }
        }

        // Quiz answers detail
        $quizAnswersDetail = collect();
        $answersLesson = null;
        $answersStudent = null;
        if ($this->showAnswersModal && $this->answersStudentId && $this->answersLessonId) {
            $answersLesson = Lesson::with('quizQuestions.options')->find($this->answersLessonId);
            $answersStudent = User::find($this->answersStudentId);
            $quizAnswersDetail = QuizAnswer::where('user_id', $this->answersStudentId)
                ->where('lesson_id', $this->answersLessonId)
                ->get()
                ->keyBy('quiz_question_id');
        }

        return view('livewire.admin.course-students', compact(
            'enrollments', 'allLessons', 'quizLessons', 'totalEnrolled',
            'completedCount', 'avgProgress', 'viewingStudent', 'studentProgress',
            'studentQuizResults', 'quizAnswersDetail', 'answersLesson', 'answersStudent'
        ))->layout('layouts.admin', ['title' => 'Siswa: ' . $this->course->title]);
    }
}
