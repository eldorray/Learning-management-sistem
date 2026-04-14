<?php

namespace App\Livewire\Student;

use App\Models\Enrollment;
use App\Models\LessonProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public bool $showEditModal = false;

    // Editable fields (all roles)
    public string $name = '';
    public string $email = '';
    public string $bio = '';
    public string $phone = '';
    public $avatar = null; // uploaded file

    // Student-only editable fields
    public string $gender = '';
    public ?string $birth_date = null;
    public string $address = '';
    public string $guardian_name = '';
    public string $class_group = '';

    // Instructor-only editable fields
    public string $specialization = '';

    // Password change
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function openEditModal(): void
    {
        $user = Auth::user();
        $this->name           = $user->name;
        $this->email          = $user->email;
        $this->bio            = $user->bio ?? '';
        $this->phone          = $user->phone ?? '';
        $this->gender         = $user->gender ?? '';
        $this->birth_date     = $user->birth_date?->format('Y-m-d');
        $this->address        = $user->address ?? '';
        $this->guardian_name  = $user->guardian_name ?? '';
        $this->class_group    = $user->class_group ?? '';
        $this->specialization = $user->specialization ?? '';
        $this->avatar         = null;
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
        $this->showEditModal  = true;
    }

    public function saveProfile(): void
    {
        $user = Auth::user();

        $emailRule = 'required|email|unique:users,email,' . $user->id;

        $rules = [
            'name'   => 'required|min:2|max:255',
            'email'  => $emailRule,
            'bio'    => 'nullable|max:500',
            'phone'  => 'nullable|max:20',
            'avatar' => 'nullable|image|max:2048',
        ];

        if ($user->isStudent()) {
            $rules['gender']        = 'nullable|in:L,P';
            $rules['birth_date']    = 'nullable|date';
            $rules['address']       = 'nullable|max:500';
            $rules['guardian_name'] = 'nullable|max:255';
            $rules['class_group']   = 'nullable|max:50';
        }

        if ($user->isInstructor()) {
            $rules['gender']         = 'nullable|in:L,P';
            $rules['birth_date']     = 'nullable|date';
            $rules['address']        = 'nullable|max:500';
            $rules['specialization'] = 'nullable|max:255';
        }

        // Password rules (only if filled)
        if ($this->new_password) {
            $rules['current_password']        = 'required';
            $rules['new_password']            = 'required|min:6|confirmed';
            $rules['new_password_confirmation'] = 'required';
        }

        $this->validate($rules, [
            'name.required'          => 'Nama wajib diisi.',
            'email.required'         => 'Email wajib diisi.',
            'email.unique'           => 'Email sudah digunakan.',
            'avatar.image'           => 'File harus berupa gambar.',
            'avatar.max'             => 'Ukuran gambar maksimal 2MB.',
            'current_password.required' => 'Kata sandi lama wajib diisi.',
            'new_password.min'       => 'Kata sandi baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // Verify current password if changing password
        if ($this->new_password) {
            if (! Hash::check($this->current_password, $user->password)) {
                $this->addError('current_password', 'Kata sandi lama tidak sesuai.');
                return;
            }
        }

        $data = [
            'name'  => $this->name,
            'email' => $this->email,
            'bio'   => $this->bio ?: null,
            'phone' => $this->phone ?: null,
        ];

        if ($user->isStudent()) {
            $data['gender']        = $this->gender ?: null;
            $data['birth_date']    = $this->birth_date ?: null;
            $data['address']       = $this->address ?: null;
            $data['guardian_name'] = $this->guardian_name ?: null;
            $data['class_group']   = $this->class_group ?: null;
        }

        if ($user->isInstructor()) {
            $data['gender']         = $this->gender ?: null;
            $data['birth_date']     = $this->birth_date ?: null;
            $data['address']        = $this->address ?: null;
            $data['specialization'] = $this->specialization ?: null;
        }

        // Avatar upload
        if ($this->avatar) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $this->avatar->store('avatars', 'public');
        }

        // Password update
        if ($this->new_password) {
            $data['password'] = Hash::make($this->new_password);
        }

        $user->update($data);

        $this->showEditModal = false;
        session()->flash('profile_success', 'Profil berhasil diperbarui!');
    }

    public function render()
    {
        $user = Auth::user();

        $enrollments = Enrollment::with('course')
            ->where('user_id', $user->id)
            ->whereHas('course', fn ($q) => $q->where('is_published', true))
            ->latest()
            ->get();

        $completedCourses = $enrollments->where('status', 'completed');
        $activeCourses    = $enrollments->where('status', 'active');

        $totalLessonsCompleted = LessonProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->count();

        $overallProgress = $enrollments->count() > 0
            ? round($enrollments->avg('progress_percentage'))
            : 0;

        return view('livewire.student.profile', compact(
            'user', 'enrollments', 'completedCourses',
            'activeCourses', 'totalLessonsCompleted', 'overallProgress'
        ))->layout('layouts.student', ['title' => 'Profil Saya']);
    }
}
