<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use App\Models\TahunAjaran;
use App\Support\AcademicYear;
use Livewire\Component;
use Livewire\WithFileUploads;

class AppSettings extends Component
{
    use WithFileUploads;

    // App identity
    public string $appName = '';
    public string $appTagline = '';
    public $appLogo = null;
    public $appFavicon = null;
    public ?string $currentLogo = null;
    public ?string $currentFavicon = null;

    // Tahun ajaran form
    public string $taNama = '';
    public string $taSemester = '1';
    public string $taMulai = '';
    public string $taSelesai = '';
    public string $taKeterangan = '';
    public bool $showTaForm = false;
    public ?int $editTaId = null;

    public function mount(): void
    {
        $this->appName    = Setting::get('app_name', 'LMS Arrahmah');
        $this->appTagline = Setting::get('app_tagline', 'Platform Pembelajaran Digital');
        $this->currentLogo    = Setting::get('app_logo');
        $this->currentFavicon = Setting::get('app_favicon');
    }

    // ── App identity ─────────────────────────────────────────────────

    public function save(): void
    {
        $this->validate([
            'appName'    => 'required|string|max:100',
            'appTagline' => 'nullable|string|max:200',
            'appLogo'    => 'nullable|image|max:2048',
            'appFavicon' => 'nullable|image|max:1024',
        ]);

        Setting::set('app_name', $this->appName);
        Setting::set('app_tagline', $this->appTagline);

        if ($this->appLogo) {
            $path = $this->appLogo->store('settings', 'public');
            Setting::set('app_logo', $path);
            $this->currentLogo = $path;
            $this->appLogo = null;
        }

        if ($this->appFavicon) {
            $path = $this->appFavicon->store('settings', 'public');
            Setting::set('app_favicon', $path);
            $this->currentFavicon = $path;
            $this->appFavicon = null;
        }

        Setting::clearCache();
        session()->flash('success', 'Pengaturan aplikasi berhasil disimpan.');
    }

    public function removeLogo(): void
    {
        Setting::set('app_logo', null);
        $this->currentLogo = null;
        Setting::clearCache();
    }

    public function removeFavicon(): void
    {
        Setting::set('app_favicon', null);
        $this->currentFavicon = null;
        Setting::clearCache();
    }

    // ── Tahun Ajaran ─────────────────────────────────────────────────

    public function setAktif(int $id): void
    {
        $ta = TahunAjaran::findOrFail($id);
        $ta->aktifkan();
        AcademicYear::clearCache();
        session()->flash('success', "Tahun ajaran {$ta->label} kini aktif.");
    }

    public function openTaForm(?int $id = null): void
    {
        $this->resetTaForm();
        if ($id) {
            $ta = TahunAjaran::findOrFail($id);
            $this->editTaId       = $ta->id;
            $this->taNama         = $ta->nama;
            $this->taSemester     = $ta->semester;
            $this->taMulai        = $ta->tanggal_mulai->format('Y-m-d');
            $this->taSelesai      = $ta->tanggal_selesai->format('Y-m-d');
            $this->taKeterangan   = $ta->keterangan ?? '';
        }
        $this->showTaForm = true;
    }

    public function saveTa(): void
    {
        $this->validate([
            'taNama'      => 'required|string|max:20',
            'taSemester'  => 'required|in:1,2',
            'taMulai'     => 'required|date',
            'taSelesai'   => 'required|date|after:taMulai',
            'taKeterangan'=> 'nullable|string|max:255',
        ], [
            'taNama.required'    => 'Nama tahun ajaran wajib diisi.',
            'taSelesai.after'    => 'Tanggal selesai harus setelah tanggal mulai.',
        ]);

        $data = [
            'nama'            => $this->taNama,
            'semester'        => $this->taSemester,
            'tanggal_mulai'   => $this->taMulai,
            'tanggal_selesai' => $this->taSelesai,
            'keterangan'      => $this->taKeterangan ?: null,
        ];

        if ($this->editTaId) {
            TahunAjaran::findOrFail($this->editTaId)->update($data);
            session()->flash('success', 'Tahun ajaran berhasil diperbarui.');
        } else {
            TahunAjaran::create($data);
            session()->flash('success', 'Tahun ajaran berhasil ditambahkan.');
        }

        $this->showTaForm = false;
        $this->resetTaForm();
        AcademicYear::clearCache();
    }

    public function deleteTa(int $id): void
    {
        $ta = TahunAjaran::findOrFail($id);
        if ($ta->is_aktif) {
            session()->flash('error', 'Tidak dapat menghapus tahun ajaran yang sedang aktif.');
            return;
        }
        $ta->delete();
        AcademicYear::clearCache();
        session()->flash('success', 'Tahun ajaran dihapus.');
    }

    private function resetTaForm(): void
    {
        $this->editTaId     = null;
        $this->taNama       = '';
        $this->taSemester   = '1';
        $this->taMulai      = '';
        $this->taSelesai    = '';
        $this->taKeterangan = '';
    }

    // ── Render ───────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.app-settings', [
            'tahunAjaranList' => TahunAjaran::orderByDesc('nama')->orderBy('semester')->get(),
        ])->layout('layouts.admin', ['title' => 'Pengaturan Aplikasi']);
    }
}
