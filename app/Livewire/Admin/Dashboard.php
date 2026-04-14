<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;

class Dashboard extends Component
{
    // ── Private helpers ─────────────────────────────────────────────

    private function scopedCourseIds(): ?Collection
    {
        $user = auth()->user();
        return $user->isInstructor()
            ? $user->instructedCourses()->pluck('id')
            : null;
    }

    private function stats(?Collection $courseIds): array
    {
        if ($courseIds !== null) {
            $studentIds         = Enrollment::whereIn('course_id', $courseIds)->distinct()->pluck('user_id');
            $totalEnrollments   = Enrollment::whereIn('course_id', $courseIds)->count();
            $completedEnrollments = Enrollment::whereIn('course_id', $courseIds)->where('status', 'completed')->count();

            return [
                'totalCourses'         => Course::where('instructor_id', auth()->id())->count(),
                'publishedCourses'     => Course::where('instructor_id', auth()->id())->where('is_published', true)->count(),
                'totalStudents'        => $studentIds->count(),
                'totalEnrollments'     => $totalEnrollments,
                'completedEnrollments' => $completedEnrollments,
                'newStudentsThisMonth' => Enrollment::whereIn('course_id', $courseIds)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->distinct()->count('user_id'),
                'avgProgress'          => Enrollment::whereIn('course_id', $courseIds)->avg('progress_percentage') ?? 0,
                '_studentIds'          => $studentIds, // passed along for login log scoping
            ];
        }

        $totalEnrollments     = Enrollment::count();
        $completedEnrollments = Enrollment::where('status', 'completed')->count();

        return [
            'totalCourses'         => Course::count(),
            'publishedCourses'     => Course::where('is_published', true)->count(),
            'totalStudents'        => User::where('role', 'student')->count(),
            'totalEnrollments'     => $totalEnrollments,
            'completedEnrollments' => $completedEnrollments,
            'newStudentsThisMonth' => User::where('role', 'student')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'avgProgress'          => Enrollment::avg('progress_percentage') ?? 0,
            '_studentIds'          => null,
        ];
    }

    private function recentEnrollments(?Collection $courseIds): Collection
    {
        $q = Enrollment::with(['user', 'course'])->latest()->take(8);
        if ($courseIds !== null) {
            $q->whereIn('course_id', $courseIds);
        }
        return $q->get();
    }

    private function popularCourses(?Collection $courseIds): \Illuminate\Database\Eloquent\Collection
    {
        $q = Course::withCount('enrollments')
            ->where('is_published', true)
            ->orderByDesc('enrollments_count')
            ->take(5);

        if ($courseIds !== null) {
            $q->where('instructor_id', auth()->id());
        }

        return $q->get();
    }

    private function loginLogs(?Collection $courseIds, ?Collection $studentIds): \Illuminate\Database\Eloquent\Collection
    {
        $q = LoginLog::with('user')->latest('logged_in_at');

        if ($courseIds !== null && $studentIds !== null) {
            $q->whereIn('user_id', $studentIds);
        }

        return $q->take(10)->get();
    }

    // ── Render ──────────────────────────────────────────────────────

    public function render()
    {
        $user      = auth()->user();
        $courseIds = $this->scopedCourseIds();
        $stats     = $this->stats($courseIds);

        $studentIds = $stats['_studentIds'];
        unset($stats['_studentIds']);

        $completionRate = $stats['totalEnrollments'] > 0
            ? round(($stats['completedEnrollments'] / $stats['totalEnrollments']) * 100)
            : 0;

        return view('livewire.admin.dashboard', [
            ...$stats,
            'completionRate'    => $completionRate,
            'recentEnrollments' => $this->recentEnrollments($courseIds),
            'popularCourses'    => $this->popularCourses($courseIds),
            'loginLogs'         => $this->loginLogs($courseIds, $studentIds),
        ])->layout('layouts.admin', ['title' => $user->isInstructor() ? 'Dashboard Guru' : 'Admin Dashboard']);
    }
}
