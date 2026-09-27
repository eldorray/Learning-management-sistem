<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('halaman login dapat diakses', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});

test('pendaftaran mandiri ditutup dan diarahkan ke login dengan penjelasan', function () {
    $this->get('/register')->assertRedirect('/login')->assertSessionHas('status');
    $this->followingRedirects()->get('/register')->assertSee('Pendaftaran mandiri ditutup');
});

test('login dibatasi setelah terlalu banyak percobaan gagal', function () {
    User::factory()->create(['email' => 'siswa@sekolah.test', 'password' => 'benar-sekali', 'role' => 'student']);

    foreach (range(1, 5) as $i) {
        \Livewire\Livewire::test(\App\Livewire\Auth\Login::class)
            ->set('identifier', 'siswa@sekolah.test')->set('password', 'salah-terus')->call('login');
    }
    \Livewire\Livewire::test(\App\Livewire\Auth\Login::class)
        ->set('identifier', 'siswa@sekolah.test')->set('password', 'benar-sekali')->call('login')
        ->assertHasErrors('identifier')->assertSee('Terlalu banyak percobaan');
    $this->assertGuest();
});

test('user yang sudah login diredirect dari halaman login', function () {
    $user = User::factory()->create(['role' => 'student']);
    $this->actingAs($user);

    $response = $this->get('/login');
    $response->assertRedirect();
});

test('user yang sudah login diredirect dari halaman register', function () {
    $user = User::factory()->create(['role' => 'student']);
    $this->actingAs($user);

    $response = $this->get('/register');
    $response->assertRedirect();
});

test('user yang belum login diredirect dari halaman dashboard student', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('user yang belum login diredirect dari halaman admin', function () {
    $response = $this->get('/admin/dashboard');
    $response->assertRedirect('/login');
});

test('student dapat mengakses dashboard', function () {
    $student = User::factory()->create(['role' => 'student']);
    $this->actingAs($student);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
});

test('student tidak dapat mengakses admin dashboard', function () {
    $student = User::factory()->create(['role' => 'student']);
    $this->actingAs($student);

    $response = $this->get('/admin/dashboard');
    $response->assertStatus(403);
});

test('admin dapat mengakses admin dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $response = $this->get('/admin/dashboard');
    $response->assertStatus(200);
});

test('instructor dapat mengakses admin dashboard', function () {
    $instructor = User::factory()->create(['role' => 'instructor']);
    $this->actingAs($instructor);

    $response = $this->get('/admin/dashboard');
    $response->assertStatus(200);
});

test('logout menghapus sesi dan redirect ke home', function () {
    $user = User::factory()->create(['role' => 'student']);
    $this->actingAs($user);

    $response = $this->post('/logout');
    $response->assertRedirect('/');
    $this->assertGuest();
});
