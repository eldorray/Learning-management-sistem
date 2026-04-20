<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user dapat membuat initials dari nama lengkap', function () {
    $user = User::factory()->make(['name' => 'Ahmad Fauzi']);
    expect($user->initials())->toBe('AF');
});

test('user initials hanya ambil 2 kata pertama', function () {
    $user = User::factory()->make(['name' => 'Siti Nur Aisyah']);
    expect($user->initials())->toBe('SN');
});

test('user dengan role admin dikenali sebagai admin', function () {
    $user = User::factory()->make(['role' => 'admin']);
    expect($user->isAdmin())->toBeTrue();
    expect($user->isStudent())->toBeFalse();
    expect($user->isInstructor())->toBeFalse();
});

test('user dengan role student dikenali sebagai student', function () {
    $user = User::factory()->make(['role' => 'student']);
    expect($user->isStudent())->toBeTrue();
    expect($user->isAdmin())->toBeFalse();
    expect($user->isInstructor())->toBeFalse();
});

test('user dengan role instructor dikenali sebagai instructor', function () {
    $user = User::factory()->make(['role' => 'instructor']);
    expect($user->isInstructor())->toBeTrue();
    expect($user->isAdmin())->toBeFalse();
    expect($user->isStudent())->toBeFalse();
});

test('user dengan role parent dikenali sebagai parent', function () {
    $user = User::factory()->make(['role' => 'parent']);
    expect($user->isParent())->toBeTrue();
});

test('avatar url menggunakan ui-avatars jika tidak ada avatar', function () {
    $user = User::factory()->make(['name' => 'Ahmad', 'avatar' => null]);
    expect($user->avatar_url)->toContain('ui-avatars.com');
    expect($user->avatar_url)->toContain(urlencode('Ahmad'));
});

test('avatar url menggunakan storage jika ada avatar', function () {
    $user = User::factory()->make(['avatar' => 'avatars/test.jpg']);
    expect($user->avatar_url)->toContain('storage/avatars/test.jpg');
});

test('user dapat cek enrollment di course', function () {
    $student = User::factory()->create(['role' => 'student']);
    $instructor = User::factory()->create(['role' => 'instructor']);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    expect($student->isEnrolledIn($course->id))->toBeFalse();

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrolled_at' => now(),
        'status' => 'active',
        'progress_percentage' => 0,
    ]);

    expect($student->fresh()->isEnrolledIn($course->id))->toBeTrue();
});

test('user dapat mendapatkan progress untuk course tertentu', function () {
    $student = User::factory()->create(['role' => 'student']);
    $instructor = User::factory()->create(['role' => 'instructor']);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    expect($student->getProgressForCourse($course->id))->toBe(0);

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrolled_at' => now(),
        'status' => 'active',
        'progress_percentage' => 75,
    ]);

    expect($student->getProgressForCourse($course->id))->toBe(75);
});

test('user memiliki relasi enrollments', function () {
    $student = User::factory()->create(['role' => 'student']);
    $instructor = User::factory()->create(['role' => 'instructor']);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrolled_at' => now(),
        'status' => 'active',
        'progress_percentage' => 0,
    ]);

    expect($student->enrollments)->toHaveCount(1);
});

test('password user disimpan dalam bentuk hashed', function () {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);
    expect($user->password)->not->toBe('secret123');
});
