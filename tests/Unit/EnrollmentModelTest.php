<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('enrollment dapat dibuat dengan data yang valid', function () {
    $student = User::factory()->create(['role' => 'student']);
    $course = Course::factory()->create();

    $enrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrolled_at' => now(),
        'status' => 'active',
        'progress_percentage' => 0,
    ]);

    expect($enrollment->id)->not->toBeNull();
    expect($enrollment->status)->toBe('active');
    expect($enrollment->progress_percentage)->toBe(0);
});

test('isCompleted mengembalikan true jika status completed', function () {
    $enrollment = new Enrollment(['status' => 'completed']);
    expect($enrollment->isCompleted())->toBeTrue();
});

test('isCompleted mengembalikan false jika status active', function () {
    $enrollment = new Enrollment(['status' => 'active']);
    expect($enrollment->isCompleted())->toBeFalse();
});

test('enrollment memiliki relasi ke user', function () {
    $student = User::factory()->create(['role' => 'student']);
    $course = Course::factory()->create();

    $enrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrolled_at' => now(),
        'status' => 'active',
        'progress_percentage' => 0,
    ]);

    expect($enrollment->user->id)->toBe($student->id);
});

test('enrollment memiliki relasi ke course', function () {
    $student = User::factory()->create(['role' => 'student']);
    $course = Course::factory()->create();

    $enrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrolled_at' => now(),
        'status' => 'active',
        'progress_percentage' => 0,
    ]);

    expect($enrollment->course->id)->toBe($course->id);
});

test('progress_percentage dapat diupdate', function () {
    $student = User::factory()->create(['role' => 'student']);
    $course = Course::factory()->create();

    $enrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrolled_at' => now(),
        'status' => 'active',
        'progress_percentage' => 0,
    ]);

    $enrollment->update(['progress_percentage' => 50, 'status' => 'active']);
    expect($enrollment->fresh()->progress_percentage)->toBe(50);
});

test('completed_at dicasting sebagai datetime', function () {
    $student = User::factory()->create(['role' => 'student']);
    $course = Course::factory()->create();

    $enrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrolled_at' => now(),
        'completed_at' => now(),
        'status' => 'completed',
        'progress_percentage' => 100,
    ]);

    expect($enrollment->completed_at)->toBeInstanceOf(\Carbon\CarbonInterface::class);
});
