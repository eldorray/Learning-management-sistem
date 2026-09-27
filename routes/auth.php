<?php

use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    // Accounts are created by the school; old links to /register land on login with an explanation.
    Route::get('/register', fn () => redirect()->route('login')
        ->with('status', 'Pendaftaran mandiri ditutup. Akun dibuat oleh sekolah — hubungi admin atau wali kelas.'))->name('register');
});

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');
