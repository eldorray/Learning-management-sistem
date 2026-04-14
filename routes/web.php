<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Student\Dashboard as StudentDashboard;
use App\Livewire\Student\CourseCatalog;
use App\Livewire\Student\CourseLearning;
use App\Livewire\Student\Profile as StudentProfile;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\CourseManagement;
use App\Livewire\Admin\CourseBuilder;
use App\Livewire\Admin\LessonEditor;
use App\Livewire\Admin\StudentDirectory;
use App\Livewire\Admin\Analytics;
use App\Livewire\Admin\InstructorDirectory;
use App\Livewire\Admin\CourseStudents;
use App\Livewire\Admin\AppSettings;
use App\Livewire\Admin\Profile as AdminProfile;

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin() || auth()->user()->isInstructor()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('student.dashboard');
    }
    return view('welcome');
})->name('home');

// Auth routes (provided by Laravel starter kit)
require __DIR__.'/auth.php';

// Student Routes — requires authenticated student role
Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/dashboard', StudentDashboard::class)->name('student.dashboard');
    Route::get('/my-courses', StudentDashboard::class)->name('student.courses');
    Route::get('/catalog', CourseCatalog::class)->name('student.catalog');
    Route::get('/learn/{slug}', CourseLearning::class)->name('student.learn');
    Route::get('/profile', StudentProfile::class)->name('student.profile');
});

// Admin Routes (shared: admin + instructor)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/courses', CourseManagement::class)->name('courses');
    Route::get('/courses/create', CourseManagement::class)->name('courses.create');
    Route::get('/courses/{courseId}/builder', CourseBuilder::class)->name('courses.builder')->where('courseId', '[0-9]+');
    Route::get('/courses/{courseId}/students', CourseStudents::class)->name('courses.students')->where('courseId', '[0-9]+');
    Route::get('/lessons/{lessonId}/edit', LessonEditor::class)->name('lesson.edit')->where('lessonId', '[0-9]+');
    Route::get('/lessons/{lessonId}/quiz', LessonEditor::class)->name('lesson.quiz')->where('lessonId', '[0-9]+');
    Route::get('/students', StudentDirectory::class)->name('students');
    Route::get('/analytics', Analytics::class)->name('analytics');
    Route::get('/profile', AdminProfile::class)->name('profile');

    // Admin-only routes
    Route::middleware('admin_only')->group(function () {
        Route::get('/instructors', InstructorDirectory::class)->name('instructors');
        Route::get('/settings', AppSettings::class)->name('settings');
    });
});
