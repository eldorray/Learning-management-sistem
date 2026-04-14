<?php

namespace App\Livewire\Student;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Enrollment;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CourseLearning extends Component
{
    public Course $course;
    public ?Lesson $currentLesson = null;
    public bool $showSidebar = true;
    public bool $showCompleteConfirm = false;

    // Quiz state
    public array $selectedAnswers = []; // [question_id => option_id]
    public array $essayAnswers = [];    // [question_id => text]
    public bool $quizSubmitted = false;
    public int $quizScore = 0;
    public int $quizTotal = 0;
    public int $quizCorrect = 0;
    public int $quizPercentage = 0;
    public bool $quizPassed = false;

    // Course completion
    public bool $courseCompleted = false;

    // Cached completed lesson IDs — loaded once, updated locally to avoid repeated queries
    private Collection $completedLessonIds;

    public function mount(string $slug): void
    {
        $this->course = Course::with(['modules.lessons', 'instructor'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        if (!Auth::user()->isEnrolledIn($this->course->id)) {
            abort(403, 'Anda belum mendaftar kursus ini.');
        }

        $this->loadCompletedIds();

        $allLessons = $this->course->modules->flatMap->lessons;
        $firstIncomplete = $allLessons->first(fn ($l) => !$this->completedLessonIds->contains($l->id));

        if ($firstIncomplete) {
            $this->selectLesson($firstIncomplete->id);
        }
    }

    // ── Internal helpers ────────────────────────────────────────────

    private function loadCompletedIds(): void
    {
        $this->completedLessonIds = LessonProgress::where('user_id', Auth::id())
            ->where('course_id', $this->course->id)
            ->where('is_completed', true)
            ->pluck('lesson_id');
    }

    private function getCompletedIds(): Collection
    {
        // Lazy-init: load from DB once per request, reuse afterwards
        if (!isset($this->completedLessonIds)) {
            $this->loadCompletedIds();
        }
        return $this->completedLessonIds;
    }

    private function resetQuizState(): void
    {
        $this->selectedAnswers  = [];
        $this->essayAnswers     = [];
        $this->quizSubmitted    = false;
        $this->quizScore        = 0;
        $this->quizTotal        = 0;
        $this->quizCorrect      = 0;
        $this->quizPercentage   = 0;
        $this->quizPassed       = false;
    }

    private function computeQuizStats(int $earned, int $total, int $correct): void
    {
        $this->quizScore      = $earned;
        $this->quizTotal      = $total;
        $this->quizCorrect    = $correct;
        $this->quizPercentage = $total > 0 ? (int) round(($earned / $total) * 100) : 0;
        $this->quizPassed     = $this->quizPercentage >= 60;
    }

    // ── Lesson navigation ───────────────────────────────────────────

    public function selectLesson(int $lessonId): void
    {
        // Allow viewing already-completed lessons (read-only)
        $this->currentLesson = Lesson::with(['quizQuestions.options'])->findOrFail($lessonId);
        $this->resetQuizState();

        if ($this->currentLesson->type === 'quiz') {
            $this->loadPreviousAnswers();
        }
    }

    private function loadPreviousAnswers(): void
    {
        $previousAnswers = QuizAnswer::where('user_id', Auth::id())
            ->where('lesson_id', $this->currentLesson->id)
            ->get();

        if ($previousAnswers->isEmpty()) {
            return;
        }

        $this->quizSubmitted = true;

        foreach ($previousAnswers as $answer) {
            if ($answer->quiz_option_id) {
                $this->selectedAnswers[$answer->quiz_question_id] = $answer->quiz_option_id;
            }
            if ($answer->essay_answer) {
                $this->essayAnswers[$answer->quiz_question_id] = $answer->essay_answer;
            }
        }

        $this->computeQuizStats(
            $previousAnswers->sum('points_earned'),
            $this->currentLesson->quizQuestions->sum('points'),
            $previousAnswers->where('is_correct', true)->count()
        );
    }

    public function nextLesson(): void
    {
        if (!$this->currentLesson) return;

        $completedIds = $this->getCompletedIds();
        $allLessons   = $this->course->modules->flatMap->lessons;
        $currentIndex = $allLessons->search(fn ($l) => $l->id === $this->currentLesson->id);

        if ($currentIndex === false) return;

        for ($i = $currentIndex + 1; $i < $allLessons->count(); $i++) {
            if (!$completedIds->contains($allLessons[$i]->id)) {
                $this->selectLesson($allLessons[$i]->id);
                return;
            }
        }
    }

    public function previousLesson(): void
    {
        if (!$this->currentLesson) return;

        $completedIds = $this->getCompletedIds();
        $allLessons   = $this->course->modules->flatMap->lessons;
        $currentIndex = $allLessons->search(fn ($l) => $l->id === $this->currentLesson->id);

        if ($currentIndex === false) return;

        for ($i = $currentIndex - 1; $i >= 0; $i--) {
            if (!$completedIds->contains($allLessons[$i]->id)) {
                $this->selectLesson($allLessons[$i]->id);
                return;
            }
        }
    }

    // ── Quiz ────────────────────────────────────────────────────────

    public function submitQuiz(): void
    {
        if (!$this->currentLesson || $this->currentLesson->type !== 'quiz') return;
        if ($this->quizSubmitted) return;

        $user      = Auth::user();
        $questions = $this->currentLesson->quizQuestions;

        foreach ($questions as $question) {
            if ($question->type !== 'essay' && !isset($this->selectedAnswers[$question->id])) {
                $this->addError('quiz', 'Silakan jawab semua pertanyaan sebelum mengirim.');
                return;
            }
        }

        $totalPoints  = 0;
        $earnedPoints = 0;
        $correctCount = 0;

        foreach ($questions as $question) {
            $totalPoints += $question->points;
            $isCorrect        = false;
            $pointsEarned     = 0;
            $selectedOptionId = null;
            $essayText        = null;

            if ($question->type === 'essay') {
                $essayText    = $this->essayAnswers[$question->id] ?? '';
                $pointsEarned = 0;
            } else {
                $selectedOptionId = $this->selectedAnswers[$question->id] ?? null;
                if ($selectedOptionId) {
                    $option = $question->options->find($selectedOptionId);
                    if ($option && $option->is_correct) {
                        $isCorrect    = true;
                        $pointsEarned = $question->points;
                        $correctCount++;
                    }
                }
            }

            QuizAnswer::updateOrCreate(
                ['user_id' => $user->id, 'quiz_question_id' => $question->id],
                [
                    'lesson_id'      => $this->currentLesson->id,
                    'quiz_option_id' => $selectedOptionId,
                    'essay_answer'   => $essayText,
                    'is_correct'     => $isCorrect,
                    'points_earned'  => $pointsEarned,
                ]
            );

            $earnedPoints += $pointsEarned;
        }

        $this->quizSubmitted = true;
        $this->computeQuizStats($earnedPoints, $totalPoints, $correctCount);

        // Award XP based on score
        if ($this->quizPercentage >= 80) {
            $user->increment('xp_points', 25);
        } elseif ($this->quizPercentage >= 50) {
            $user->increment('xp_points', 15);
        } else {
            $user->increment('xp_points', 5);
        }

        // Auto-mark complete if score >= 60%
        if ($this->quizPassed) {
            $this->markComplete();
        }
    }

    public function retryQuiz(): void
    {
        if (!$this->currentLesson) return;

        $user = Auth::user();

        QuizAnswer::where('user_id', $user->id)
            ->where('lesson_id', $this->currentLesson->id)
            ->delete();

        LessonProgress::where('user_id', $user->id)
            ->where('lesson_id', $this->currentLesson->id)
            ->update(['is_completed' => false, 'completed_at' => null]);

        // Refresh local cache
        $this->loadCompletedIds();
        $this->resetQuizState();
        $this->course->recalculateProgressFor(Auth::id());
    }

    // ── Lesson completion ───────────────────────────────────────────

    public function confirmMarkComplete(): void
    {
        if (!$this->currentLesson) return;
        if ($this->getCompletedIds()->contains($this->currentLesson->id)) return;

        $this->showCompleteConfirm = true;
    }

    public function markComplete(): void
    {
        if (!$this->currentLesson) return;

        $user = Auth::user();

        if ($this->getCompletedIds()->contains($this->currentLesson->id)) {
            $this->showCompleteConfirm = false;
            return;
        }

        LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $this->currentLesson->id],
            ['course_id' => $this->course->id, 'is_completed' => true, 'completed_at' => now()]
        );

        // Refresh local cache so other methods don't re-query
        $this->loadCompletedIds();

        if ($this->currentLesson->type !== 'quiz') {
            $user->increment('xp_points', 10);
        }

        // Single centralized progress calculation
        $this->course->recalculateProgressFor($user->id);

        $this->showCompleteConfirm = false;
        $this->dispatch('lesson-completed');
    }

    public function completeCourse(): void
    {
        $user       = Auth::user();
        $allLessons = $this->course->modules->flatMap->lessons;

        foreach ($allLessons as $lesson) {
            LessonProgress::updateOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $lesson->id],
                ['course_id' => $this->course->id, 'is_completed' => true, 'completed_at' => now()]
            );
        }

        $this->loadCompletedIds();
        $this->course->recalculateProgressFor($user->id);
        $this->courseCompleted = true;
        session()->flash('success', 'Selamat! Anda telah menyelesaikan kursus "' . $this->course->title . '"!');
    }

    // ── Render ──────────────────────────────────────────────────────

    public function render()
    {
        $user             = Auth::user();
        $completedLessonIds = $this->getCompletedIds();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $this->course->id)
            ->first();

        $isCurrentCompleted = $this->currentLesson
            ? $completedLessonIds->contains($this->currentLesson->id)
            : false;

        $allLessons         = $this->course->modules->flatMap->lessons;
        $isLastLesson       = $this->currentLesson
            ? $allLessons->last()?->id === $this->currentLesson->id
            : false;
        $allLessonsCompleted = $allLessons->count() > 0
            && $completedLessonIds->count() >= $allLessons->count();
        $isCourseCompleted  = $enrollment?->status === 'completed';

        return view('livewire.student.course-learning', compact(
            'completedLessonIds', 'enrollment', 'isCurrentCompleted',
            'isLastLesson', 'allLessonsCompleted', 'isCourseCompleted'
        ))->layout('layouts.student', ['title' => $this->course->title]);
    }
}
