<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $identifier = '';
    public string $password = '';
    public bool $remember = false;
    public bool $showPassword = false;

    protected function rules(): array
    {
        return [
            'identifier' => 'required|string',
            'password'   => 'required|min:6',
        ];
    }

    protected function messages(): array
    {
        return [
            'identifier.required' => 'Email atau NISN wajib diisi.',
            'password.required'   => 'Kata sandi wajib diisi.',
            'password.min'        => 'Kata sandi minimal 6 karakter.',
        ];
    }

    public function login(): void
    {
        $this->validate();

        if (filter_var($this->identifier, FILTER_VALIDATE_EMAIL)) {
            // Login dengan email
            if (Auth::attempt(['email' => $this->identifier, 'password' => $this->password], $this->remember)) {
                session()->regenerate();

                $user = Auth::user();
                if ($user->isAdmin() || $user->isInstructor()) {
                    $this->redirect(route('admin.dashboard'), navigate: true);
                } elseif ($user->isParent()) {
                    $this->redirect(route('parent.dashboard'), navigate: true);
                } else {
                    $this->redirect(route('student.dashboard'), navigate: true);
                }
                return;
            }

            $this->addError('identifier', 'Email atau kata sandi salah.');
            return;
        }

        // Login dengan NISN (hanya siswa)
        $user = User::where('nisn', $this->identifier)->where('role', 'student')->first();

        if ($user && Auth::attempt(['email' => $user->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            $this->redirect(route('student.dashboard'), navigate: true);
            return;
        }

        $this->addError('identifier', 'NISN atau kata sandi salah.');
    }

    public function togglePassword(): void
    {
        $this->showPassword = ! $this->showPassword;
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.guest', ['title' => 'Masuk']);
    }
}
