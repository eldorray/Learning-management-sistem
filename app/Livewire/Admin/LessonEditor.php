<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\AuthorizesCourseOwnership;
use App\Models\Lesson;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use Livewire\Component;

class LessonEditor extends Component
{
    use AuthorizesCourseOwnership;
    public Lesson $lesson;

    // Lesson form fields
    public string $lessonTitle = '';
    public string $lessonType = 'text';
    public string $lessonContent = '';
    public string $lessonVideoUrl = '';
    public int $lessonDuration = 0;
    public bool $lessonIsPreview = false;

    // Quiz question form
    public bool $showQuestionForm = false;
    public ?int $editingQuestionId = null;
    public string $questionText = '';
    public string $questionType = 'multiple_choice';
    public string $questionExplanation = '';
    public int $questionPoints = 10;
    public array $options = [];

    // Delete confirm
    public bool $showDeleteQuestionModal = false;
    public ?int $deletingQuestionId = null;

    public function mount(int $lessonId): void
    {
        $this->lesson = Lesson::with(['module', 'course', 'quizQuestions.options'])->findOrFail($lessonId);
        $this->authorizeCourseOwnership($this->lesson->course);

        // Populate lesson fields
        $this->lessonTitle = $this->lesson->title;
        $this->lessonType = $this->lesson->type;
        $this->lessonContent = $this->lesson->content ?? '';
        $this->lessonVideoUrl = $this->lesson->video_url ?? '';
        $this->lessonDuration = $this->lesson->duration_minutes;
        $this->lessonIsPreview = $this->lesson->is_preview;
    }

    // ── Lesson save ─────────────────────────────────────────────────

    public function saveLesson(): void
    {
        $this->validate([
            'lessonTitle'    => 'required|min:2|max:255',
            'lessonType'     => 'required|in:text,video,quiz',
            'lessonContent'  => 'nullable',
            'lessonVideoUrl' => 'nullable|url',
            'lessonDuration' => 'integer|min:0',
        ]);

        $this->lesson->update([
            'title'            => $this->lessonTitle,
            'type'             => $this->lessonType,
            'content'          => $this->lessonContent,
            'video_url'        => $this->lessonVideoUrl ?: null,
            'duration_minutes' => $this->lessonDuration,
            'is_preview'       => $this->lessonIsPreview,
        ]);

        session()->flash('success', 'Pelajaran berhasil diperbarui!');
        $this->refreshLesson();
    }

    // ── Quiz Question CRUD ──────────────────────────────────────────

    public function openAddQuestion(): void
    {
        $this->editingQuestionId = null;
        $this->questionText = '';
        $this->questionType = 'multiple_choice';
        $this->questionExplanation = '';
        $this->questionPoints = 10;
        $this->options = [
            ['text' => '', 'is_correct' => true],
            ['text' => '', 'is_correct' => false],
            ['text' => '', 'is_correct' => false],
            ['text' => '', 'is_correct' => false],
        ];
        $this->showQuestionForm = true;
    }

    public function openEditQuestion(int $questionId): void
    {
        $question = QuizQuestion::with('options')->findOrFail($questionId);
        $this->editingQuestionId = $questionId;
        $this->questionText = $question->question;
        $this->questionType = $question->type;
        $this->questionExplanation = $question->explanation ?? '';
        $this->questionPoints = $question->points;

        if ($question->type === 'true_false') {
            $this->options = [
                ['text' => 'Benar', 'is_correct' => $question->options->firstWhere('option_text', 'Benar')?->is_correct ?? true],
                ['text' => 'Salah', 'is_correct' => $question->options->firstWhere('option_text', 'Salah')?->is_correct ?? false],
            ];
        } else {
            $this->options = $question->options->map(fn ($opt) => [
                'text' => $opt->option_text,
                'is_correct' => $opt->is_correct,
            ])->toArray();

            // Ensure at least 2 options
            while (count($this->options) < 2) {
                $this->options[] = ['text' => '', 'is_correct' => false];
            }
        }

        $this->showQuestionForm = true;
    }

    public function updatedQuestionType(): void
    {
        if ($this->questionType === 'true_false') {
            $this->options = [
                ['text' => 'Benar', 'is_correct' => true],
                ['text' => 'Salah', 'is_correct' => false],
            ];
        } elseif ($this->questionType === 'multiple_choice' && count($this->options) < 2) {
            $this->options = [
                ['text' => '', 'is_correct' => true],
                ['text' => '', 'is_correct' => false],
                ['text' => '', 'is_correct' => false],
                ['text' => '', 'is_correct' => false],
            ];
        }
    }

    public function addOption(): void
    {
        if (count($this->options) < 6) {
            $this->options[] = ['text' => '', 'is_correct' => false];
        }
    }

    public function removeOption(int $index): void
    {
        if (count($this->options) > 2) {
            array_splice($this->options, $index, 1);
            $this->options = array_values($this->options);
        }
    }

    public function setCorrectOption(int $index): void
    {
        foreach ($this->options as $i => &$opt) {
            $opt['is_correct'] = ($i === $index);
        }
    }

    public function saveQuestion(): void
    {
        $rules = [
            'questionText'   => 'required|min:3',
            'questionType'   => 'required|in:multiple_choice,true_false,essay',
            'questionPoints' => 'integer|min:1|max:100',
        ];

        // Validate options for non-essay types
        if ($this->questionType !== 'essay') {
            $rules['options'] = 'required|array|min:2';
            $rules['options.*.text'] = 'required|min:1';
        }

        $this->validate($rules);

        // Ensure exactly one correct answer for non-essay types
        if ($this->questionType !== 'essay') {
            $hasCorrect = collect($this->options)->contains('is_correct', true);
            if (!$hasCorrect) {
                $this->addError('options', 'Pilih setidaknya satu jawaban benar.');
                return;
            }
        }

        $data = [
            'lesson_id'   => $this->lesson->id,
            'question'    => $this->questionText,
            'type'        => $this->questionType,
            'explanation' => $this->questionExplanation ?: null,
            'points'      => $this->questionPoints,
        ];

        if ($this->editingQuestionId) {
            $question = QuizQuestion::findOrFail($this->editingQuestionId);
            $question->update($data);
        } else {
            $order = $this->lesson->quizQuestions()->max('order') + 1;
            $question = QuizQuestion::create(array_merge($data, ['order' => $order]));
        }

        // Save options for non-essay types
        if ($this->questionType !== 'essay') {
            // Delete existing options first
            $question->options()->delete();

            foreach ($this->options as $i => $opt) {
                QuizOption::create([
                    'quiz_question_id' => $question->id,
                    'option_text'      => $opt['text'],
                    'is_correct'       => $opt['is_correct'],
                    'order'            => $i,
                ]);
            }
        }

        $this->showQuestionForm = false;
        $this->refreshLesson();
        session()->flash('success', 'Pertanyaan berhasil disimpan!');
    }

    public function confirmDeleteQuestion(int $questionId): void
    {
        $this->deletingQuestionId = $questionId;
        $this->showDeleteQuestionModal = true;
    }

    public function deleteQuestion(): void
    {
        if ($this->deletingQuestionId) {
            QuizQuestion::findOrFail($this->deletingQuestionId)->delete();
            $this->refreshLesson();
        }
        $this->showDeleteQuestionModal = false;
        $this->deletingQuestionId = null;
    }

    public function moveQuestionUp(int $questionId): void
    {
        $questions = $this->lesson->quizQuestions()->orderBy('order')->get();
        $index = $questions->search(fn ($q) => $q->id === $questionId);
        if ($index > 0) {
            $current = $questions[$index];
            $prev = $questions[$index - 1];
            [$current->order, $prev->order] = [$prev->order, $current->order];
            $current->save();
            $prev->save();
            $this->refreshLesson();
        }
    }

    public function moveQuestionDown(int $questionId): void
    {
        $questions = $this->lesson->quizQuestions()->orderBy('order')->get();
        $index = $questions->search(fn ($q) => $q->id === $questionId);
        if ($index < $questions->count() - 1) {
            $current = $questions[$index];
            $next = $questions[$index + 1];
            [$current->order, $next->order] = [$next->order, $current->order];
            $current->save();
            $next->save();
            $this->refreshLesson();
        }
    }

    // ── Helpers ─────────────────────────────────────────────────────

    private function refreshLesson(): void
    {
        $this->lesson = Lesson::with(['module', 'course', 'quizQuestions' => fn ($q) => $q->orderBy('order'), 'quizQuestions.options' => fn ($q) => $q->orderBy('order')])
            ->findOrFail($this->lesson->id);
    }

    public function render()
    {
        return view('livewire.admin.lesson-editor')
            ->layout('layouts.admin', ['title' => 'Editor: ' . $this->lesson->title]);
    }
}
