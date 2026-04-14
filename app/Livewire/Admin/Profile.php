<?php

namespace App\Livewire\Admin;

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
    public $avatar = null;

    // Instructor-only editable fields
    public string $gender = '';
    public ?string $birth_date = null;
    public string $address = '';
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

        $rules = [
            'name'   => 'required|min:2|max:255',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'bio'    => 'nullable|max:500',
            'phone'  => 'nullable|max:20',
            'avatar' => 'nullable|image|max:2048',
        ];

        if ($user->isInstructor()) {
            $rules['gender']         = 'nullable|in:L,P';
            $rules['birth_date']     = 'nullable|date';
            $rules['address']        = 'nullable|max:500';
            $rules['specialization'] = 'nullable|max:255';
        }

        if ($this->new_password) {
            $rules['current_password']          = 'required';
            $rules['new_password']              = 'required|min:6|confirmed';
            $rules['new_password_confirmation'] = 'required';
        }

        $this->validate($rules, [
            'name.required'             => 'Nama wajib diisi.',
            'email.required'            => 'Email wajib diisi.',
            'email.unique'              => 'Email sudah digunakan.',
            'avatar.image'              => 'File harus berupa gambar.',
            'avatar.max'                => 'Ukuran gambar maksimal 2MB.',
            'current_password.required' => 'Kata sandi lama wajib diisi.',
            'new_password.min'          => 'Kata sandi baru minimal 6 karakter.',
            'new_password.confirmed'    => 'Konfirmasi kata sandi tidak cocok.',
        ]);

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

        if ($user->isInstructor()) {
            $data['gender']         = $this->gender ?: null;
            $data['birth_date']     = $this->birth_date ?: null;
            $data['address']        = $this->address ?: null;
            $data['specialization'] = $this->specialization ?: null;
        }

        if ($this->avatar) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $this->avatar->store('avatars', 'public');
        }

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

        return view('livewire.admin.profile', compact('user'))
            ->layout('layouts.admin', ['title' => 'Profil Saya']);
    }
}
