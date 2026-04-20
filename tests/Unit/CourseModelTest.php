<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('course otomatis generate enrollment_code saat dibuat', function () {
    $instructor = User::factory()->create(['role' => 'instructor']);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    expect($course->enrollment_code)->not->toBeNull();
    expect(strlen($course->enrollment_code))->toBe(6);
});

test('enrollment_code bersifat unik antar course', function () {
    $courses = Course::factory()->count(5)->create();
    $codes = $courses->pluck('enrollment_code')->toArray();

    expect(array_unique($codes))->toHaveCount(5);
});

test('level badge menampilkan label yang benar', function () {
    $course = Course::factory()->make(['level' => 'beginner']);
    expect($course->level_badge)->toBe('Pemula');

    $course->level = 'intermediate';
    expect($course->level_badge)->toBe('Menengah');

    $course->level = 'advanced';
    expect($course->level_badge)->toBe('Mahir');
});

test('formatted duration menampilkan jam dan menit dengan benar', function () {
    $course = Course::factory()->make(['duration_minutes' => 90]);
    expect($course->formatted_duration)->toBe('1j 30m');

    $course->duration_minutes = 45;
    expect($course->formatted_duration)->toBe('45m');

    $course->duration_minutes = 120;
    expect($course->formatted_duration)->toBe('2j 0m');
});

test('course memiliki relasi ke instructor', function () {
    $instructor = User::factory()->create(['role' => 'instructor']);
    $course = Course::factory()->create(['instructor_id' => $instructor->id]);

    expect($course->instructor->id)->toBe($instructor->id);
    expect($course->instructor->name)->toBe($instructor->name);
});

test('course memiliki relasi enrollments', function () {
    $student = User::factory()->create(['role' => 'student']);
    $course = Course::factory()->create();

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrolled_at' => now(),
        'status' => 'active',
        'progress_percentage' => 0,
    ]);

    expect($course->enrollments)->toHaveCount(1);
});

test('getLessonCount mengembalikan jumlah lesson yang benar', function () {
    $course = Course::factory()->create(['total_lessons' => 0]);

    expect($course->getLessonCount())->toBe(0);
});

test('generateUniqueCode menghasilkan string 6 karakter uppercase', function () {
    $code = Course::generateUniqueCode();
    expect($code)->toHaveLength(6);
    expect($code)->toBe(strtoupper($code));
});
