<?php

namespace App\Livewire\Student;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Notification;
use App\Support\AcademicYear;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CourseCatalog extends Component
{
    use WithPagination;

    public string $search = '';
    public string $level = '';
    public string $category = '';
    public string $sortBy = 'latest';

    // Enrollment code modal
    public bool $showCodeModal = false;
    public ?int $enrollingCourseId = null;
    public string $enrollmentCode = '';

    protected $queryString = ['search', 'level', 'category', 'sortBy'];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLevel(): void
    {
        $this->resetPage();
    }

    public function openEnrollModal(int $courseId): void
    {
        $this->enrollingCourseId = $courseId;
        $this->enrollmentCode = '';
        $this->resetErrorBag();
        $this->showCodeModal = true;
    }

    public function enroll(): void
    {
        $this->validate([
            'enrollmentCode' => 'required|string',
        ], [
            'enrollmentCode.required' => 'Kode pendaftaran wajib diisi.',
        ]);

        $user = Auth::user();
        $course = Course::findOrFail($this->enrollingCourseId);

        // Verify code
        if (strtoupper(trim($this->enrollmentCode)) !== $course->enrollment_code) {
            $this->addError('enrollmentCode', 'Kode pendaftaran tidak valid. Silakan minta kode yang benar kepada instruktur.');
            return;
        }

        if ($user->isEnrolledIn($course->id)) {
            $this->showCodeModal = false;
            return;
        }

        Enrollment::create([
            'user_id'              => $user->id,
            'course_id'            => $course->id,
            'tahun_ajaran_id'      => AcademicYear::aktifId(),
            'enrolled_at'          => now(),
            'progress_percentage'  => 0,
            'status'               => 'active',
        ]);

        Notification::send(
            userId: $user->id,
            type: 'course_enrolled',
            title: 'Berhasil mendaftar kursus!',
            message: 'Kamu telah bergabung di kursus "' . $course->title . '". Selamat belajar!',
            icon: 'auto_stories',
            url: route('student.learn', $course->slug),
        );

        $this->showCodeModal = false;
        $this->enrollmentCode = '';
        session()->flash('success', 'Berhasil mendaftar kursus "' . $course->title . '"!');
        $this->redirect(route('student.dashboard'));
    }

    public function render()
    {
        $query = Course::with('instructor')
            ->withCount('enrollments')
            ->where('is_published', true);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('category', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->level) {
            $query->where('level', $this->level);
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        $query->when($this->sortBy === 'popular', fn($q) => $q->orderByDesc('enrollments_count'))
              ->when($this->sortBy === 'latest', fn($q) => $q->latest())
              ->when($this->sortBy === 'title', fn($q) => $q->orderBy('title'));

        $courses = $query->paginate(12);

        $categories = Course::where('is_published', true)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        $enrollments = Auth::user()->enrollments()->get(['course_id', 'status']);
        $enrolledIds = $enrollments->pluck('course_id');
        $completedIds = $enrollments->where('status', 'completed')->pluck('course_id');

        return view('livewire.student.course-catalog', compact('courses', 'categories', 'enrolledIds', 'completedIds'))
            ->layout('layouts.student', ['title' => 'Katalog Kursus']);
    }
}
