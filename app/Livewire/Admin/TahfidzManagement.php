<?php

namespace App\Livewire\Admin;

use App\Models\Surah;
use App\Models\TahfidzGroup;
use App\Models\TahfidzRecord;
use App\Models\TahfidzTarget;
use App\Models\User;
use App\Support\AcademicYear;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class TahfidzManagement extends Component
{
    use WithPagination;

    public string $activeTab = 'dashboard'; // dashboard | targets | groups | records

    // Target form
    public bool   $showTargetModal = false;
    public ?int   $editTargetId    = null;
    public string $targetKelas     = '';
    public string $targetSemester  = '1';
    public string $targetSurahMulaiId  = '';
    public int    $targetAyatMulai     = 1;
    public string $targetSurahSelesaiId = '';
    public int    $targetAyatSelesai    = 1;
    public string $targetKeterangan    = '';

    // Group form
    public bool   $showGroupModal  = false;
    public ?int   $editGroupId     = null;
    public string $groupNama       = '';
    public string $groupKelas      = '';
    public string $groupInstrukturId = '';
    public string $groupDeskripsi  = '';

    // Plotting (assign students to group)
    public bool   $showPlottingModal = false;
    public ?int   $plottingGroupId   = null;
    public array  $selectedStudents  = [];

    // Filters
    public string $searchRecord = '';
    public string $filterSurah  = '';
    public string $filterJenis  = '';

    protected function rules(): array
    {
        return [
            'targetKelas'          => 'required|string|max:50',
            'targetSemester'       => 'required|in:1,2',
            'targetSurahMulaiId'   => 'required|exists:surahs,id',
            'targetAyatMulai'      => 'required|integer|min:1',
            'targetSurahSelesaiId' => 'required|exists:surahs,id',
            'targetAyatSelesai'    => 'required|integer|min:1',
            'targetKeterangan'     => 'nullable|string|max:500',
        ];
    }

    // ── Dashboard Stats ──────────────────────────────────────────────

    private function taScope(\Illuminate\Database\Eloquent\Builder $q): \Illuminate\Database\Eloquent\Builder
    {
        $taId = AcademicYear::aktifId();
        return $taId ? $q->where('tahun_ajaran_id', $taId) : $q;
    }

    public function getTotalSetoranProperty(): int
    {
        return $this->taScope(TahfidzRecord::query())->count();
    }

    public function getTotalSiswaAktifProperty(): int
    {
        return $this->taScope(TahfidzRecord::query())->distinct('student_id')->count('student_id');
    }

    public function getTotalHalaqohProperty(): int
    {
        return $this->taScope(TahfidzGroup::where('is_active', true))->count();
    }

    public function getAvgScoreProperty(): int
    {
        $avg = $this->taScope(TahfidzRecord::query())
            ->avg(DB::raw('(score_kelancaran + score_tajwid + score_makhorijul_huruf) / 3'));
        return (int) round($avg ?? 0);
    }

    public function getTopStudentsProperty()
    {
        $taId = AcademicYear::aktifId();
        return User::where('role', 'student')
            ->withCount(['tahfidzRecords as total_setoran' => fn ($q) => $taId ? $q->where('tahun_ajaran_id', $taId) : $q])
            ->withAvg(['tahfidzRecords as avg_score' => fn ($q) => $taId ? $q->where('tahun_ajaran_id', $taId) : $q],
                DB::raw('(score_kelancaran + score_tajwid + score_makhorijul_huruf) / 3'))
            ->having('total_setoran', '>', 0)
            ->orderByDesc('total_setoran')
            ->limit(5)
            ->get();
    }

    public function getRecentRecordsProperty()
    {
        return $this->taScope(TahfidzRecord::with(['student', 'instruktur', 'surah']))
            ->latest('tanggal_setoran')
            ->limit(10)
            ->get();
    }

    public function getSetoranByJenisProperty()
    {
        return $this->taScope(TahfidzRecord::select('jenis_setoran', DB::raw('count(*) as total')))
            ->groupBy('jenis_setoran')
            ->get()
            ->keyBy('jenis_setoran');
    }

    // ── Targets CRUD ─────────────────────────────────────────────────

    public function openTargetModal(?int $id = null): void
    {
        $this->resetTargetForm();
        if ($id) {
            $target = TahfidzTarget::findOrFail($id);
            $this->editTargetId           = $target->id;
            $this->targetKelas            = $target->tingkat_kelas;
            $this->targetSemester         = $target->semester;
            $this->targetSurahMulaiId     = (string) $target->surah_mulai_id;
            $this->targetAyatMulai        = $target->ayat_mulai;
            $this->targetSurahSelesaiId   = (string) $target->surah_selesai_id;
            $this->targetAyatSelesai      = $target->ayat_selesai;
            $this->targetKeterangan       = $target->keterangan ?? '';
        }
        $this->showTargetModal = true;
    }

    public function saveTarget(): void
    {
        $this->validate([
            'targetKelas'          => 'required|string|max:50',
            'targetSemester'       => 'required|in:1,2',
            'targetSurahMulaiId'   => 'required|exists:surahs,id',
            'targetAyatMulai'      => 'required|integer|min:1',
            'targetSurahSelesaiId' => 'required|exists:surahs,id',
            'targetAyatSelesai'    => 'required|integer|min:1',
        ]);

        $data = [
            'tingkat_kelas'    => $this->targetKelas,
            'semester'         => $this->targetSemester,
            'surah_mulai_id'   => $this->targetSurahMulaiId,
            'ayat_mulai'       => $this->targetAyatMulai,
            'surah_selesai_id' => $this->targetSurahSelesaiId,
            'ayat_selesai'     => $this->targetAyatSelesai,
            'keterangan'       => $this->targetKeterangan ?: null,
        ];

        if ($this->editTargetId) {
            TahfidzTarget::findOrFail($this->editTargetId)->update($data);
        } else {
            $data['tahun_ajaran_id'] = AcademicYear::aktifId();
            TahfidzTarget::create($data);
        }

        $this->showTargetModal = false;
        $this->resetTargetForm();
        session()->flash('success', 'Target hafalan berhasil disimpan.');
    }

    public function deleteTarget(int $id): void
    {
        TahfidzTarget::findOrFail($id)->delete();
        session()->flash('success', 'Target dihapus.');
    }

    private function resetTargetForm(): void
    {
        $this->editTargetId = null;
        $this->targetKelas = $this->targetSemester = $this->targetKeterangan = '';
        $this->targetSurahMulaiId = $this->targetSurahSelesaiId = '';
        $this->targetAyatMulai = $this->targetAyatSelesai = 1;
    }

    // ── Groups CRUD ───────────────────────────────────────────────────

    public function openGroupModal(?int $id = null): void
    {
        $this->resetGroupForm();
        if ($id) {
            $group = TahfidzGroup::findOrFail($id);
            $this->editGroupId       = $group->id;
            $this->groupNama         = $group->nama_halaqoh;
            $this->groupKelas        = $group->tingkat_kelas ?? '';
            $this->groupInstrukturId = (string) $group->instruktur_id;
            $this->groupDeskripsi    = $group->deskripsi ?? '';
        }
        $this->showGroupModal = true;
    }

    public function saveGroup(): void
    {
        $this->validate([
            'groupNama'         => 'required|string|max:100',
            'groupInstrukturId' => 'required|exists:users,id',
            'groupKelas'        => 'nullable|string|max:50',
            'groupDeskripsi'    => 'nullable|string|max:500',
        ]);

        $data = [
            'nama_halaqoh'  => $this->groupNama,
            'instruktur_id' => $this->groupInstrukturId,
            'tingkat_kelas' => $this->groupKelas ?: null,
            'deskripsi'     => $this->groupDeskripsi ?: null,
        ];

        if ($this->editGroupId) {
            TahfidzGroup::findOrFail($this->editGroupId)->update($data);
        } else {
            $data['tahun_ajaran_id'] = AcademicYear::aktifId();
            TahfidzGroup::create($data);
        }

        $this->showGroupModal = false;
        $this->resetGroupForm();
        session()->flash('success', 'Halaqoh berhasil disimpan.');
    }

    public function deleteGroup(int $id): void
    {
        TahfidzGroup::findOrFail($id)->delete();
        session()->flash('success', 'Halaqoh dihapus.');
    }

    public function openPlottingModal(int $groupId): void
    {
        $this->plottingGroupId  = $groupId;
        $group = TahfidzGroup::with('students')->findOrFail($groupId);
        $this->selectedStudents = $group->students->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $this->showPlottingModal = true;
    }

    public function savePlotting(): void
    {
        if (!$this->plottingGroupId) return;
        $group = TahfidzGroup::findOrFail($this->plottingGroupId);
        $sync  = collect($this->selectedStudents)->filter()->mapWithKeys(fn($id) => [$id => ['joined_at' => now()->format('Y-m-d')]])->toArray();
        $group->students()->sync($sync);
        $this->showPlottingModal = false;
        session()->flash('success', 'Plotting siswa berhasil disimpan.');
    }

    private function resetGroupForm(): void
    {
        $this->editGroupId = null;
        $this->groupNama = $this->groupKelas = $this->groupInstrukturId = $this->groupDeskripsi = '';
    }

    // ── Render ────────────────────────────────────────────────────────

    public function render()
    {
        $taId = AcademicYear::aktifId();
        $tahunAjaran = AcademicYear::aktif();

        return view('livewire.admin.tahfidz-management', [
            'surahs'      => Surah::orderBy('nomor')->get(),
            'tahunAjaran' => $tahunAjaran,
            'targets'     => TahfidzTarget::with(['surahMulai', 'surahSelesai'])
                ->when($taId, fn ($q) => $q->where('tahun_ajaran_id', $taId))
                ->orderBy('tingkat_kelas')->get(),
            'groups'      => TahfidzGroup::with(['instruktur'])->withCount('students')
                ->when($taId, fn ($q) => $q->where('tahun_ajaran_id', $taId))
                ->orderBy('nama_halaqoh')->get(),
            'instructors' => User::where('role', 'instructor')->orderBy('name')->get(),
            'allStudents' => User::where('role', 'student')->orderBy('name')->get(),
            'records'     => TahfidzRecord::with(['student', 'instruktur', 'surah'])
                ->when($taId, fn ($q) => $q->where('tahun_ajaran_id', $taId))
                ->when($this->searchRecord, fn($q) => $q->whereHas('student', fn($sq) => $sq->where('name', 'like', "%{$this->searchRecord}%")))
                ->when($this->filterSurah, fn($q) => $q->where('surah_id', $this->filterSurah))
                ->when($this->filterJenis, fn($q) => $q->where('jenis_setoran', $this->filterJenis))
                ->latest('tanggal_setoran')
                ->paginate(15),
        ])->layout('layouts.admin', ['title' => 'Manajemen Tahfidz']);
    }
}
