<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('halaman login dapat diakses', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});

test('halaman register dapat diakses', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);
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
