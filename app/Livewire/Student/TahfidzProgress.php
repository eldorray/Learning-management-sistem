<?php

namespace App\Livewire\Student;

use App\Models\Surah;
use App\Models\TahfidzRecord;
use App\Models\TahfidzTarget;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TahfidzProgress extends Component
{
    public string $activeTab = 'progress'; // progress | riwayat | portfolio

    public function getRecordsProperty()
    {
        return TahfidzRecord::with(['surah', 'instruktur'])
            ->where('student_id', auth()->id())
            ->latest('tanggal_setoran')
            ->get();
    }

    public function getLatestRecordsProperty()
    {
        return $this->records->take(5);
    }

    public function getTotalZiyadahProperty(): int
    {
        return $this->records->where('jenis_setoran', 'ziyadah')->count();
    }

    public function getTotalMurojaahProperty(): int
    {
        return $this->records->where('jenis_setoran', 'murojaah')->count();
    }

    public function getAvgScoreProperty(): int
    {
        if ($this->records->isEmpty()) return 0;
        return (int) round($this->records->avg(fn($r) => ($r->score_kelancaran + $r->score_tajwid + $r->score_makhorijul_huruf) / 3));
    }

    public function getCompletedSurahsProperty()
    {
        // Surah dianggap selesai jika ada setoran ziyadah yang mencakup seluruh ayat
        return TahfidzRecord::where('student_id', auth()->id())
            ->where('jenis_setoran', 'ziyadah')
            ->with('surah')
            ->get()
            ->groupBy('surah_id')
            ->filter(function ($records, $surahId) {
                $surah = $records->first()->surah;
                if (!$surah) return false;
                // Check if all ayat covered
                $coveredAyat = collect();
                foreach ($records as $r) {
                    for ($i = $r->ayat_mulai; $i <= $r->ayat_selesai; $i++) {
                        $coveredAyat->push($i);
                    }
                }
                return $coveredAyat->unique()->count() >= $surah->jumlah_ayat;
            })
            ->map(fn($records) => $records->first()->surah);
    }

    public function getProgressToTargetProperty(): int
    {
        $completedCount = $this->completed_surahs->count();
        // Calculate against Juz 30 (37 surahs: 78-114)
        $targetSurahs = Surah::whereBetween('nomor', [78, 114])->count();
        if ($targetSurahs === 0) return 0;
        $completedInTarget = $this->completed_surahs->filter(fn($s) => $s->nomor >= 78 && $s->nomor <= 114)->count();
        return (int) round(($completedInTarget / $targetSurahs) * 100);
    }

    public function getSurahMapProperty()
    {
        $studentRecords = TahfidzRecord::where('student_id', auth()->id())
            ->select('surah_id', DB::raw('count(*) as total'), DB::raw('max(jenis_setoran) as jenis'))
            ->groupBy('surah_id')
            ->get()
            ->keyBy('surah_id');

        return Surah::orderBy('nomor')->get()->map(function ($surah) use ($studentRecords) {
            $record = $studentRecords->get($surah->id);
            $surah->state = $record
                ? ($this->completed_surahs->contains('id', $surah->id) ? 'completed' : 'in_progress')
                : 'not_started';
            return $surah;
        });
    }

    public function getStreakDaysProperty(): int
    {
        $dates = TahfidzRecord::where('student_id', auth()->id())
            ->orderByDesc('tanggal_setoran')
            ->pluck('tanggal_setoran')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->unique()
            ->values();

        $streak = 0;
        $today  = now()->format('Y-m-d');

        foreach ($dates as $i => $date) {
            $expected = now()->subDays($i)->format('Y-m-d');
            if ($date === $expected) {
                $streak++;
            } else {
                break;
            }
        }
        return $streak;
    }

    public function render()
    {
        return view('livewire.student.tahfidz-progress', [
            'allRecords'  => $this->records,
            'target'      => TahfidzTarget::with(['surahMulai', 'surahSelesai'])
                ->where('tingkat_kelas', auth()->user()->class_group ?? 'Kelas 7')
                ->first(),
        ])->layout('layouts.student', ['title' => 'Tahfidz Saya']);
    }
}
