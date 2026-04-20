<?php

namespace App\Livewire\Instructor;

use App\Models\Notification;
use App\Models\Surah;
use App\Models\TahfidzGroup;
use App\Models\TahfidzRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class TahfidzHalaqoh extends Component
{
    use WithPagination;

    public string $activeTab     = 'halaqoh'; // halaqoh | setoran | riwayat
    public ?int   $selectedGroup = null;
    public ?int   $viewStudentId = null;

    // Setoran form
    public bool   $showSetoranModal = false;
    public int    $setoranStudentId = 0;
    public string $setoranSurahId   = '';
    public int    $setoranAyatMulai = 1;
    public int    $setoranAyatSelesai = 1;
    public string $setoranJenis     = 'ziyadah';
    public int    $setoranKelancaran= 80;
    public int    $setoranTajwid    = 80;
    public int    $setoranMakhorijul= 80;
    public string $setoranKeterangan= '';
    public string $setoranTanggal  = '';

    // Riwayat filters
    public string $searchRiwayat = '';
    public string $filterSurah   = '';
    public string $filterJenis   = '';

    public function mount(): void
    {
        $this->setoranTanggal = now()->format('Y-m-d');

        // Auto select first group
        $firstGroup = TahfidzGroup::where('instruktur_id', auth()->id())->first();
        if ($firstGroup) $this->selectedGroup = $firstGroup->id;
    }

    public function getMyGroupsProperty()
    {
        return TahfidzGroup::where('instruktur_id', auth()->id())
            ->with(['students' => function ($q) {
                $q->withCount('tahfidzRecords as total_setoran')
                  ->withAvg('tahfidzRecords as avg_score', DB::raw('(score_kelancaran + score_tajwid + score_makhorijul_huruf) / 3'));
            }])
            ->withCount('students')
            ->get();
    }

    public function getSelectedGroupDataProperty(): ?TahfidzGroup
    {
        if (!$this->selectedGroup) return null;
        return TahfidzGroup::with(['students' => function ($q) {
            $q->withCount('tahfidzRecords as total_setoran')
              ->withAvg('tahfidzRecords as avg_score', DB::raw('(score_kelancaran + score_tajwid + score_makhorijul_huruf) / 3'));
        }])->find($this->selectedGroup);
    }

    public function getStudentDetailProperty(): ?User
    {
        if (!$this->viewStudentId) return null;
        return User::with([
            'tahfidzRecords' => fn($q) => $q->with('surah')->latest('tanggal_setoran')->limit(10),
        ])->find($this->viewStudentId);
    }

    public function getStudentProgressProperty(): int
    {
        if (!$this->viewStudentId) return 0;
        $total = TahfidzRecord::where('student_id', $this->viewStudentId)
            ->where('jenis_setoran', 'ziyadah')
            ->distinct('surah_id')
            ->count('surah_id');
        return min(100, (int) round(($total / 114) * 100));
    }

    public function openSetoranModal(int $studentId): void
    {
        $this->setoranStudentId = $studentId;
        $this->setoranSurahId   = '';
        $this->setoranAyatMulai = 1;
        $this->setoranAyatSelesai = 1;
        $this->setoranJenis       = 'ziyadah';
        $this->setoranKelancaran  = 80;
        $this->setoranTajwid      = 80;
        $this->setoranMakhorijul  = 80;
        $this->setoranKeterangan  = '';
        $this->setoranTanggal     = now()->format('Y-m-d');
        $this->showSetoranModal   = true;
    }

    public function saveSetoran(): void
    {
        $this->validate([
            'setoranStudentId'  => 'required|exists:users,id',
            'setoranSurahId'    => 'required|exists:surahs,id',
            'setoranAyatMulai'  => 'required|integer|min:1',
            'setoranAyatSelesai'=> 'required|integer|min:1|gte:setoranAyatMulai',
            'setoranJenis'      => 'required|in:ziyadah,murojaah',
            'setoranKelancaran' => 'required|integer|min:0|max:100',
            'setoranTajwid'     => 'required|integer|min:0|max:100',
            'setoranMakhorijul' => 'required|integer|min:0|max:100',
            'setoranTanggal'    => 'required|date',
        ]);

        TahfidzRecord::create([
            'student_id'             => $this->setoranStudentId,
            'instruktur_id'          => auth()->id(),
            'surah_id'               => $this->setoranSurahId,
            'ayat_mulai'             => $this->setoranAyatMulai,
            'ayat_selesai'           => $this->setoranAyatSelesai,
            'jenis_setoran'          => $this->setoranJenis,
            'score_kelancaran'       => $this->setoranKelancaran,
            'score_tajwid'           => $this->setoranTajwid,
            'score_makhorijul_huruf' => $this->setoranMakhorijul,
            'keterangan'             => $this->setoranKeterangan ?: null,
            'status'                 => 'approved',
            'tanggal_setoran'        => $this->setoranTanggal,
        ]);

        $surah = Surah::find($this->setoranSurahId);
        $avgScore = intdiv($this->setoranKelancaran + $this->setoranTajwid + $this->setoranMakhorijul, 3);
        Notification::send(
            userId: $this->setoranStudentId,
            type: 'tahfidz_graded',
            title: 'Setoran tahfidz dinilai!',
            message: "Setoran {$this->setoranJenis} {$surah?->nama_latin} ayat {$this->setoranAyatMulai}-{$this->setoranAyatSelesai} mendapat nilai rata-rata {$avgScore}.",
            icon: 'menu_book',
            url: route('student.tahfidz'),
        );

        $this->showSetoranModal = false;
        session()->flash('success', 'Setoran berhasil dicatat.');
    }

    public function deleteRecord(int $id): void
    {
        $record = TahfidzRecord::findOrFail($id);
        if ($record->instruktur_id !== auth()->id()) return;
        $record->delete();
        session()->flash('success', 'Setoran dihapus.');
    }

    public function updatedSetoranSurahId(): void
    {
        if ($this->setoranSurahId) {
            $surah = Surah::find($this->setoranSurahId);
            if ($surah) $this->setoranAyatSelesai = $surah->jumlah_ayat;
        }
    }

    public function render()
    {
        return view('livewire.instructor.tahfidz-halaqoh', [
            'surahs'  => Surah::orderBy('nomor')->get(),
            'riwayat' => TahfidzRecord::with(['student', 'surah'])
                ->where('instruktur_id', auth()->id())
                ->when($this->searchRiwayat, fn($q) => $q->whereHas('student', fn($sq) => $sq->where('name', 'like', "%{$this->searchRiwayat}%")))
                ->when($this->filterSurah, fn($q) => $q->where('surah_id', $this->filterSurah))
                ->when($this->filterJenis, fn($q) => $q->where('jenis_setoran', $this->filterJenis))
                ->latest('tanggal_setoran')
                ->paginate(15),
        ])->layout('layouts.admin', ['title' => 'Halaqoh Tahfidz']);
    }
}
