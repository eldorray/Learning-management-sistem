<?php

namespace App\Livewire\ParentPortal;

use App\Models\TahfidzRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public ?int   $selectedChildId = null;
    public string $activeTab       = 'overview'; // overview | kursus | tahfidz | profil

    public function mount(): void
    {
        // Auto-select first child on load
        $first = auth()->user()->children()->first();
        if ($first) {
            $this->selectedChildId = $first->id;
        }
    }

    public function getChildrenProperty()
    {
        return auth()->user()->children()->orderBy('name')->get();
    }

    public function getSelectedChildProperty(): ?User
    {
        if (!$this->selectedChildId) return null;

        return User::with([
            'enrollments.course',
            'tahfidzRecords' => fn ($q) => $q->with('surah')->latest('tanggal_setoran'),
        ])->find($this->selectedChildId);
    }

    // ── Kursus stats ──────────────────────────────────────────────────
    public function getEnrollmentStatsProperty(): array
    {
        $child = $this->selectedChild;
        if (!$child) return ['total' => 0, 'selesai' => 0, 'aktif' => 0, 'avg_progress' => 0];

        $enrollments = $child->enrollments;
        return [
            'total'        => $enrollments->count(),
            'selesai'      => $enrollments->where('status', 'completed')->count(),
            'aktif'        => $enrollments->where('status', 'active')->count(),
            'avg_progress' => (int) round($enrollments->avg('progress_percentage') ?? 0),
        ];
    }

    // ── Tahfidz stats ─────────────────────────────────────────────────
    public function getTahfidzStatsProperty(): array
    {
        $child = $this->selectedChild;
        if (!$child) return ['total_setoran' => 0, 'ziyadah' => 0, 'murojaah' => 0, 'avg_score' => 0, 'streak' => 0];

        $records = $child->tahfidzRecords;

        // streak calculation
        $dates  = $records->pluck('tanggal_setoran')->map(fn($d) => $d->format('Y-m-d'))->unique()->sort()->reverse()->values();
        $streak = 0;
        foreach ($dates as $i => $date) {
            if ($date === now()->subDays($i)->format('Y-m-d')) {
                $streak++;
            } else {
                break;
            }
        }

        return [
            'total_setoran' => $records->count(),
            'ziyadah'       => $records->where('jenis_setoran', 'ziyadah')->count(),
            'murojaah'      => $records->where('jenis_setoran', 'murojaah')->count(),
            'avg_score'     => $records->isEmpty() ? 0 : (int) round($records->avg(fn($r) => ($r->score_kelancaran + $r->score_tajwid + $r->score_makhorijul_huruf) / 3)),
            'streak'        => $streak,
        ];
    }

    public function getRecentTahfidzProperty()
    {
        if (!$this->selectedChildId) return collect();
        return TahfidzRecord::with(['surah', 'instruktur'])
            ->where('student_id', $this->selectedChildId)
            ->latest('tanggal_setoran')
            ->limit(8)
            ->get();
    }

    public function getTahfidzMonthlyTrendProperty()
    {
        if (!$this->selectedChildId) return collect();
        return TahfidzRecord::where('student_id', $this->selectedChildId)
            ->where('tanggal_setoran', '>=', now()->subMonths(6))
            ->select(
                DB::raw('YEAR(tanggal_setoran) as year'),
                DB::raw('MONTH(tanggal_setoran) as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('AVG((score_kelancaran + score_tajwid + score_makhorijul_huruf) / 3.0) as avg_score')
            )
            ->groupBy(DB::raw('YEAR(tanggal_setoran)'), DB::raw('MONTH(tanggal_setoran)'))
            ->orderBy(DB::raw('YEAR(tanggal_setoran)'))->orderBy(DB::raw('MONTH(tanggal_setoran)'))
            ->get()
            ->map(function ($item) {
                $item->label     = \Carbon\Carbon::create($item->year, $item->month)->translatedFormat('M Y');
                $item->avg_score = (int) round($item->avg_score);
                return $item;
            });
    }

    public function selectChild(int $id): void
    {
        // Ensure this child actually belongs to the logged-in parent
        if (auth()->user()->children()->where('users.id', $id)->exists()) {
            $this->selectedChildId = $id;
            $this->activeTab = 'overview';
        }
    }

    public function render()
    {
        return view('livewire.parent.dashboard', [
            'children'            => $this->children,
            'selectedChild'       => $this->selectedChild,
            'enrollmentStats'     => $this->enrollmentStats,
            'tahfidzStats'        => $this->tahfidzStats,
            'recentTahfidz'       => $this->recentTahfidz,
            'tahfidzMonthlyTrend' => $this->tahfidzMonthlyTrend,
        ])->layout('layouts.parent', ['title' => 'Portal Orang Tua']);
    }
}
