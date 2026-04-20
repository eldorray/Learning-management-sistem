<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Support\AcademicYear;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class CourseManagement extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterLevel = '';
    public string $filterStatus = '';

    public bool $showForm = false;
    public bool $showDeleteModal = false;
    public ?int $editingCourseId = null;
    public ?int $deletingCourseId = null;

    // Form fields
    public string $title = '';
    public string $description = '';
    public string $short_description = '';
    public string $level = 'beginner';
    public string $category = '';
    public int $duration_minutes = 0;
    public bool $is_free = true;
    public float $price = 0;
    public bool $is_published = false;
    public $thumbnail = null;

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'description' => 'nullable',
        'short_description' => 'nullable|max:500',
        'level' => 'required|in:beginner,intermediate,advanced',
        'category' => 'nullable|max:100',
        'duration_minutes' => 'integer|min:0',
        'is_free' => 'boolean',
        'price' => 'numeric|min:0',
        'is_published' => 'boolean',
        'thumbnail' => 'nullable|image|max:2048',
    ];

    /**
     * Abort 403 if the current instructor does not own the given course.
     * Admins always pass through.
     */
    private function authorizeOwnership(Course $course): void
    {
        if (auth()->user()->isInstructor() && $course->instructor_id !== auth()->id()) {
            abort(403);
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateForm(): void
    {
        $this->reset(['title', 'description', 'short_description', 'level', 'category',
                      'duration_minutes', 'is_free', 'price', 'is_published', 'thumbnail']);
        $this->editingCourseId = null;
        $this->showForm = true;
    }

    public function openEditForm(int $courseId): void
    {
        $course = Course::findOrFail($courseId);
        $this->authorizeOwnership($course);

        $this->editingCourseId = $courseId;
        $this->title = $course->title;
        $this->description = $course->description ?? '';
        $this->short_description = $course->short_description ?? '';
        $this->level = $course->level;
        $this->category = $course->category ?? '';
        $this->duration_minutes = $course->duration_minutes;
        $this->is_free = $course->is_free;
        $this->price = $course->price;
        $this->is_published = $course->is_published;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'slug' => Str::slug($this->title) . '-' . Str::random(4),
            'description' => $this->description,
            'short_description' => $this->short_description,
            'level' => $this->level,
            'category' => $this->category,
            'duration_minutes' => $this->duration_minutes,
            'is_free' => $this->is_free,
            'price' => $this->is_free ? 0 : $this->price,
            'is_published' => $this->is_published,
            'instructor_id' => auth()->id(),
        ];

        if ($this->thumbnail) {
            $data['thumbnail'] = $this->thumbnail->store('thumbnails', 'public');
        }

        if ($this->editingCourseId) {
            $course = Course::findOrFail($this->editingCourseId);
            $this->authorizeOwnership($course);

            unset($data['slug']); // Don't change slug on update
            unset($data['instructor_id']); // Don't change owner on update
            $course->update($data);
            session()->flash('success', 'Kursus berhasil diperbarui!');
        } else {
            $data['tahun_ajaran_id'] = AcademicYear::aktifId();
            Course::create($data);
            session()->flash('success', 'Kursus berhasil dibuat!');
        }

        $this->showForm = false;
        $this->reset(['title', 'description', 'short_description', 'level', 'category',
                      'duration_minutes', 'is_free', 'price', 'is_published', 'thumbnail']);
    }

    public function confirmDelete(int $courseId): void
    {
        $course = Course::findOrFail($courseId);
        $this->authorizeOwnership($course);

        $this->deletingCourseId = $courseId;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingCourseId) {
            $course = Course::findOrFail($this->deletingCourseId);
            $this->authorizeOwnership($course);

            $course->delete();
            session()->flash('success', 'Kursus berhasil dihapus.');
        }
        $this->showDeleteModal = false;
        $this->deletingCourseId = null;
    }

    public function togglePublish(int $courseId): void
    {
        $course = Course::findOrFail($courseId);
        $this->authorizeOwnership($course);

        $course->update(['is_published' => !$course->is_published]);
    }

    public function regenerateCode(int $courseId): void
    {
        $course = Course::findOrFail($courseId);
        $this->authorizeOwnership($course);

        $course->update(['enrollment_code' => Course::generateUniqueCode()]);
        session()->flash('success', 'Kode pendaftaran baru: ' . $course->enrollment_code);
    }

    public function render()
    {
        $user = auth()->user();
        $taId = AcademicYear::aktifId();
        $query = Course::with('instructor')->withCount('enrollments')
            ->when($taId, fn ($q) => $q->where('tahun_ajaran_id', $taId));

        // Instructor only sees their own courses
        if ($user->isInstructor()) {
            $query->where('instructor_id', $user->id);
        }

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }
        if ($this->filterLevel) {
            $query->where('level', $this->filterLevel);
        }
        if ($this->filterStatus === 'published') {
            $query->where('is_published', true);
        } elseif ($this->filterStatus === 'draft') {
            $query->where('is_published', false);
        }

        $courses = $query->latest()->paginate(10);

        return view('livewire.admin.course-management', compact('courses'))
            ->layout('layouts.admin', ['title' => $user->isInstructor() ? 'Kursus Saya' : 'Manajemen Kursus']);
    }
}
