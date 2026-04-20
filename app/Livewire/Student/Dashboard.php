<?php

namespace App\Livewire\Student;

use App\Models\Enrollment;
use App\Models\Course;
use App\Support\AcademicYear;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $tahunAjaran = AcademicYear::aktif();
        $tahunAjaranId = $tahunAjaran?->id;

        // Base enrollment query helper
        $enrollmentBase = fn () => Enrollment::where('user_id', $user->id)
            ->when($tahunAjaranId, fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->whereHas('course', fn ($q) => $q->where('is_published', true));

        $enrollments = $enrollmentBase()
            ->with('course.instructor')
            ->where('status', 'active')
            ->latest()
            ->take(4)
            ->get();

        $completedCount = $enrollmentBase()
            ->where('status', 'completed')
            ->count();

        $totalEnrolled = $enrollmentBase()->count();

        $inProgressCount = $enrollmentBase()
            ->where('status', 'active')
            ->where('progress_percentage', '>', 0)
            ->count();

        $recommendedCourses = Course::where('is_published', true)
            ->when($tahunAjaranId, fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->whereNotIn('id', Enrollment::where('user_id', $user->id)->pluck('course_id'))
            ->withCount('enrollments')
            ->latest()
            ->take(3)
            ->get();

        // Kecenderungan pelajaran: hitung kategori kursus berdasarkan enrollment & progress
        $kecenderungan = $this->hitungKecenderungan($user->id, $tahunAjaranId);

        return view('livewire.student.dashboard', compact(
            'user', 'enrollments', 'completedCount',
            'totalEnrolled', 'inProgressCount', 'recommendedCourses',
            'kecenderungan', 'tahunAjaran'
        ))->layout('layouts.student', ['title' => 'Dashboard']);
    }

    /**
     * Hitung kecenderungan pelajaran siswa berdasarkan:
     * - Jumlah kursus per kategori yang diikuti
     * - Rata-rata progres per kategori
     * - Kursus yang diselesaikan per kategori
     *
     * Returns collection of categories sorted by "minat score" descending.
     */
    private function hitungKecenderungan(int $userId, ?int $tahunAjaranId): \Illuminate\Support\Collection
    {
        $enrollments = Enrollment::where('user_id', $userId)
            ->when($tahunAjaranId, fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->with('course:id,title,category,level')
            ->whereHas('course', fn ($q) => $q->where('is_published', true))
            ->get();

        if ($enrollments->isEmpty()) {
            return collect();
        }

        // Kelompokkan per kategori dan hitung skor minat
        return $enrollments
            ->groupBy(fn ($e) => $e->course->category ?? 'Umum')
            ->map(function ($items, $kategori) {
                $jumlah = $items->count();
                $avgProgress = $items->avg('progress_percentage');
                $selesai = $items->where('status', 'completed')->count();

                // Skor minat = jumlah kursus (bobot 40%) + avg progres (bobot 40%) + bonus selesai (bobot 20%)
                $skor = ($jumlah * 40) + ($avgProgress * 0.4) + ($selesai * 20);

                return [
                    'kategori' => $kategori,
                    'jumlah_kursus' => $jumlah,
                    'avg_progress' => round($avgProgress),
                    'selesai' => $selesai,
                    'skor' => round($skor),
                    'level_dominan' => $items->groupBy(fn ($e) => $e->course->level)
                        ->sortByDesc(fn ($g) => $g->count())
                        ->keys()
                        ->first() ?? 'beginner',
                ];
            })
            ->sortByDesc('skor')
            ->values()
            ->take(5);
    }
}
