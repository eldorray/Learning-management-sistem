<?php

namespace App\Livewire\Student;

use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        $enrollments = Enrollment::with('course.instructor')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->whereHas('course', fn ($q) => $q->where('is_published', true))
            ->latest()
            ->take(4)
            ->get();

        $completedCount = Enrollment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('course', fn ($q) => $q->where('is_published', true))
            ->count();

        $totalEnrolled = Enrollment::where('user_id', $user->id)
            ->whereHas('course', fn ($q) => $q->where('is_published', true))
            ->count();

        $inProgressCount = Enrollment::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('progress_percentage', '>', 0)
            ->whereHas('course', fn ($q) => $q->where('is_published', true))
            ->count();

        $recommendedCourses = Course::where('is_published', true)
            ->whereNotIn('id', Enrollment::where('user_id', $user->id)->pluck('course_id'))
            ->withCount('enrollments')
            ->latest()
            ->take(3)
            ->get();

        return view('livewire.student.dashboard', compact(
            'user', 'enrollments', 'completedCount',
            'totalEnrolled', 'inProgressCount', 'recommendedCourses'
        ))->layout('layouts.student', ['title' => 'Dashboard']);
    }
}
