<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Livewire\Component;

class Analytics extends Component
{
    public string $period = '30'; // days

    // ── Private helpers ─────────────────────────────────────────────

    private function scopedCourseIds(): ?Collection
    {
        $user = auth()->user();
        return $user->isInstructor()
            ? $user->instructedCourses()->pluck('id')
            : null;
    }

    private function totalStats(?Collection $courseIds): array
    {
        if ($courseIds !== null) {
            return [
                'totalStudents'    => Enrollment::whereIn('course_id', $courseIds)->distinct()->count('user_id'),
                'totalCourses'     => Course::where('instructor_id', auth()->id())->where('is_published', true)->count(),
                'totalEnrollments' => Enrollment::whereIn('course_id', $courseIds)->count(),
                'totalCompleted'   => Enrollment::whereIn('course_id', $courseIds)->where('status', 'completed')->count(),
            ];
        }

        return [
            'totalStudents'    => User::where('role', 'student')->count(),
            'totalCourses'     => Course::where('is_published', true)->count(),
            'totalEnrollments' => Enrollment::count(),
            'totalCompleted'   => Enrollment::where('status', 'completed')->count(),
        ];
    }

    private function newStudents(?Collection $courseIds, \DateTimeInterface $from): int
    {
        if ($courseIds !== null) {
            return Enrollment::whereIn('course_id', $courseIds)
                ->where('created_at', '>=', $from)
                ->distinct()->count('user_id');
        }

        return User::where('role', 'student')->where('created_at', '>=', $from)->count();
    }

    private function newEnrollments(?Collection $courseIds, \DateTimeInterface $from): int
    {
        $q = Enrollment::where('created_at', '>=', $from);
        if ($courseIds !== null) {
            $q->whereIn('course_id', $courseIds);
        }
        return $q->count();
    }

    private function coursePerformance(?Collection $courseIds): \Illuminate\Database\Eloquent\Collection
    {
        $q = Course::withCount('enrollments')
            ->withAvg('enrollments as avg_progress', 'progress_percentage')
            ->where('is_published', true)
            ->orderByDesc('enrollments_count')
            ->take(10);

        if ($courseIds !== null) {
            $q->where('instructor_id', auth()->id());
        }

        return $q->get();
    }

    private function enrollmentTrend(?Collection $courseIds): \Illuminate\Support\Collection
    {
        $q = Enrollment::select(
                DB::raw("CAST(strftime('%Y', created_at) AS INTEGER) as year"),
                DB::raw("CAST(strftime('%m', created_at) AS INTEGER) as month"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6));

        if ($courseIds !== null) {
            $q->whereIn('course_id', $courseIds);
        }

        return $q->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get()
            ->map(function ($item) {
                $item->label = Carbon::create($item->year, $item->month)->format('M Y');
                return $item;
            });
    }

    private function levelDistribution(?Collection $courseIds): \Illuminate\Database\Eloquent\Collection
    {
        $q = Course::where('is_published', true)
            ->select('level', DB::raw('COUNT(*) as count'));

        if ($courseIds !== null) {
            $q->where('instructor_id', auth()->id());
        }

        return $q->groupBy('level')->get();
    }

    private function topStudents(?Collection $courseIds): \Illuminate\Database\Eloquent\Collection
    {
        $q = User::where('role', 'student')
            ->withCount(['enrollments', 'enrollments as completed_count' => fn ($sq) => $sq->where('status', 'completed')])
            ->orderByDesc('xp_points')
            ->take(5);

        if ($courseIds !== null) {
            $studentIds = Enrollment::whereIn('course_id', $courseIds)->distinct()->pluck('user_id');
            $q->whereIn('id', $studentIds);
        }

        return $q->get();
    }

    // ── Render ──────────────────────────────────────────────────────

    public function render()
    {
        $from      = now()->subDays((int) $this->period);
        $courseIds = $this->scopedCourseIds();

        $stats           = $this->totalStats($courseIds);
        $completionRate  = $stats['totalEnrollments'] > 0
            ? round(($stats['totalCompleted'] / $stats['totalEnrollments']) * 100)
            : 0;

        return view('livewire.admin.analytics', [
            ...$stats,
            'completionRate'    => $completionRate,
            'newStudents'       => $this->newStudents($courseIds, $from),
            'newEnrollments'    => $this->newEnrollments($courseIds, $from),
            'coursePerformance' => $this->coursePerformance($courseIds),
            'enrollmentTrend'   => $this->enrollmentTrend($courseIds),
            'levelDistribution' => $this->levelDistribution($courseIds),
            'topStudents'       => $this->topStudents($courseIds),
        ])->layout('layouts.admin', ['title' => 'Analitik & Laporan']);
    }
}
