<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\AuthorizesCourseOwnership;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use Livewire\Component;

class CourseBuilder extends Component
{
    use AuthorizesCourseOwnership;
    public Course $course;

    // Module form
    public bool $showModuleForm = false;
    public ?int $editingModuleId = null;
    public string $moduleName = '';

    // Lesson form
    public bool $showLessonForm = false;
    public ?int $editingLessonId = null;
    public ?int $lessonModuleId = null;
    public string $lessonTitle = '';
    public string $lessonType = 'text'; // text, video, quiz
    public string $lessonContent = '';
    public string $lessonVideoUrl = '';
    public int $lessonDuration = 0;
    public bool $lessonIsPreview = false;

    // Delete confirm
    public bool $showDeleteModuleModal = false;
    public ?int $deletingModuleId = null;
    public bool $showDeleteLessonModal = false;
    public ?int $deletingLessonId = null;

    // Drag & reorder state
    public ?int $expandedModuleId = null;

    public function mount(int $courseId): void
    {
        $this->course = Course::with(['modules.lessons'])->findOrFail($courseId);
        $this->authorizeCourseOwnership($this->course);

        // Auto-expand first module
        $this->expandedModuleId = $this->course->modules->first()?->id;
    }

    // ── Module actions ──────────────────────────────────────────────

    public function openAddModule(): void
    {
        $this->editingModuleId = null;
        $this->moduleName = '';
        $this->showModuleForm = true;
    }

    public function openEditModule(int $moduleId): void
    {
        $module = Module::findOrFail($moduleId);
        $this->editingModuleId = $moduleId;
        $this->moduleName = $module->title;
        $this->showModuleForm = true;
    }

    public function saveModule(): void
    {
        $this->validate(['moduleName' => 'required|min:2|max:255']);

        if ($this->editingModuleId) {
            Module::findOrFail($this->editingModuleId)->update(['title' => $this->moduleName]);
        } else {
            $order = $this->course->modules()->max('order') + 1;
            $module = Module::create([
                'course_id' => $this->course->id,
                'title' => $this->moduleName,
                'order' => $order,
            ]);
            $this->expandedModuleId = $module->id;
        }

        $this->showModuleForm = false;
        $this->moduleName = '';
        $this->refreshCourse();
    }

    public function confirmDeleteModule(int $moduleId): void
    {
        $this->deletingModuleId = $moduleId;
        $this->showDeleteModuleModal = true;
    }

    public function deleteModule(): void
    {
        if ($this->deletingModuleId) {
            Module::findOrFail($this->deletingModuleId)->delete();
            $this->refreshCourse();
            $this->updateCourseLessonCount();
        }
        $this->showDeleteModuleModal = false;
        $this->deletingModuleId = null;
    }

    public function toggleModule(int $moduleId): void
    {
        $this->expandedModuleId = $this->expandedModuleId === $moduleId ? null : $moduleId;
    }

    public function moveModuleUp(int $moduleId): void
    {
        $modules = $this->course->modules()->orderBy('order')->get();
        $index = $modules->search(fn ($m) => $m->id === $moduleId);
        if ($index > 0) {
            $current = $modules[$index];
            $prev = $modules[$index - 1];
            [$current->order, $prev->order] = [$prev->order, $current->order];
            $current->save();
            $prev->save();
            $this->refreshCourse();
        }
    }

    public function moveModuleDown(int $moduleId): void
    {
        $modules = $this->course->modules()->orderBy('order')->get();
        $index = $modules->search(fn ($m) => $m->id === $moduleId);
        if ($index < $modules->count() - 1) {
            $current = $modules[$index];
            $next = $modules[$index + 1];
            [$current->order, $next->order] = [$next->order, $current->order];
            $current->save();
            $next->save();
            $this->refreshCourse();
        }
    }

    // ── Lesson actions ──────────────────────────────────────────────

    public function openAddLesson(int $moduleId): void
    {
        $this->editingLessonId = null;
        $this->lessonModuleId = $moduleId;
        $this->lessonTitle = '';
        $this->lessonType = 'text';
        $this->lessonContent = '';
        $this->lessonVideoUrl = '';
        $this->lessonDuration = 0;
        $this->lessonIsPreview = false;
        $this->expandedModuleId = $moduleId;
        $this->showLessonForm = true;
    }

    public function openEditLesson(int $lessonId): void
    {
        $lesson = Lesson::findOrFail($lessonId);
        $this->editingLessonId = $lessonId;
        $this->lessonModuleId = $lesson->module_id;
        $this->lessonTitle = $lesson->title;
        $this->lessonType = $lesson->type;
        $this->lessonContent = $lesson->content ?? '';
        $this->lessonVideoUrl = $lesson->video_url ?? '';
        $this->lessonDuration = $lesson->duration_minutes;
        $this->lessonIsPreview = $lesson->is_preview;
        $this->showLessonForm = true;
    }

    public function saveLesson(): void
    {
        $this->validate([
            'lessonTitle'    => 'required|min:2|max:255',
            'lessonType'     => 'required|in:text,video,quiz',
            'lessonContent'  => 'nullable',
            'lessonVideoUrl' => 'nullable|url',
            'lessonDuration' => 'integer|min:0',
        ]);

        $data = [
            'module_id'        => $this->lessonModuleId,
            'course_id'        => $this->course->id,
            'title'            => $this->lessonTitle,
            'type'             => $this->lessonType,
            'content'          => $this->lessonContent,
            'video_url'        => $this->lessonVideoUrl ?: null,
            'duration_minutes' => $this->lessonDuration,
            'is_preview'       => $this->lessonIsPreview,
        ];

        if ($this->editingLessonId) {
            Lesson::findOrFail($this->editingLessonId)->update($data);
            $savedLessonId = $this->editingLessonId;
        } else {
            $order = Lesson::where('module_id', $this->lessonModuleId)->max('order') + 1;
            $lesson = Lesson::create(array_merge($data, ['order' => $order]));
            $savedLessonId = $lesson->id;
        }

        $this->updateCourseLessonCount();
        $this->showLessonForm = false;
        $this->refreshCourse();

        // If quiz type, redirect to quiz editor
        if ($this->lessonType === 'quiz') {
            $this->redirect(route('admin.lesson.quiz', $savedLessonId));
        }
    }

    public function confirmDeleteLesson(int $lessonId): void
    {
        $this->deletingLessonId = $lessonId;
        $this->showDeleteLessonModal = true;
    }

    public function deleteLesson(): void
    {
        if ($this->deletingLessonId) {
            Lesson::findOrFail($this->deletingLessonId)->delete();
            $this->refreshCourse();
            $this->updateCourseLessonCount();
        }
        $this->showDeleteLessonModal = false;
        $this->deletingLessonId = null;
    }

    public function moveLessonUp(int $lessonId): void
    {
        $lesson = Lesson::findOrFail($lessonId);
        $lessons = Lesson::where('module_id', $lesson->module_id)->orderBy('order')->get();
        $index = $lessons->search(fn ($l) => $l->id === $lessonId);
        if ($index > 0) {
            $prev = $lessons[$index - 1];
            [$lesson->order, $prev->order] = [$prev->order, $lesson->order];
            $lesson->save();
            $prev->save();
            $this->refreshCourse();
        }
    }

    public function moveLessonDown(int $lessonId): void
    {
        $lesson = Lesson::findOrFail($lessonId);
        $lessons = Lesson::where('module_id', $lesson->module_id)->orderBy('order')->get();
        $index = $lessons->search(fn ($l) => $l->id === $lessonId);
        if ($index < $lessons->count() - 1) {
            $next = $lessons[$index + 1];
            [$lesson->order, $next->order] = [$next->order, $lesson->order];
            $lesson->save();
            $next->save();
            $this->refreshCourse();
        }
    }

    // ── Helpers ─────────────────────────────────────────────────────

    private function refreshCourse(): void
    {
        $this->course = Course::with(['modules' => fn ($q) => $q->orderBy('order'),
                                      'modules.lessons' => fn ($q) => $q->orderBy('order')])
            ->findOrFail($this->course->id);
    }

    private function updateCourseLessonCount(): void
    {
        $this->course->syncLessonCount();

        // Recalculate progress for all enrolled students
        $enrolledUserIds = \App\Models\Enrollment::where('course_id', $this->course->id)
            ->pluck('user_id');

        foreach ($enrolledUserIds as $userId) {
            $this->course->recalculateProgressFor($userId);
        }
    }

    public function render()
    {
        return view('livewire.admin.course-builder')
            ->layout('layouts.admin', ['title' => 'Builder: ' . $this->course->title]);
    }
}
