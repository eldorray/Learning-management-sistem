<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;

class AppSettings extends Component
{
    use WithFileUploads;

    public string $appName = '';
    public string $appTagline = '';
    public $appLogo = null;
    public $appFavicon = null;

    // Current images
    public ?string $currentLogo = null;
    public ?string $currentFavicon = null;

    public function mount(): void
    {
        $this->appName = Setting::get('app_name', 'LMS Arrahmah');
        $this->appTagline = Setting::get('app_tagline', 'Platform Pembelajaran Digital');
        $this->currentLogo = Setting::get('app_logo');
        $this->currentFavicon = Setting::get('app_favicon');
    }

    public function save(): void
    {
        $this->validate([
            'appName' => 'required|string|max:100',
            'appTagline' => 'nullable|string|max:200',
            'appLogo' => 'nullable|image|max:2048',
            'appFavicon' => 'nullable|image|max:1024',
        ], [
            'appName.required' => 'Nama aplikasi wajib diisi.',
            'appLogo.image' => 'Logo harus berupa gambar.',
            'appLogo.max' => 'Logo maksimal 2MB.',
            'appFavicon.image' => 'Favicon harus berupa gambar.',
            'appFavicon.max' => 'Favicon maksimal 1MB.',
        ]);

        Setting::set('app_name', $this->appName);
        Setting::set('app_tagline', $this->appTagline);

        // Handle logo upload
        if ($this->appLogo) {
            $logoPath = $this->appLogo->store('settings', 'public');
            Setting::set('app_logo', $logoPath);
            $this->currentLogo = $logoPath;
            $this->appLogo = null;
        }

        // Handle favicon upload
        if ($this->appFavicon) {
            $faviconPath = $this->appFavicon->store('settings', 'public');
            Setting::set('app_favicon', $faviconPath);
            $this->currentFavicon = $faviconPath;
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

    public function render()
    {
        return view('livewire.admin.app-settings')
            ->layout('layouts.admin', ['title' => 'Pengaturan Aplikasi']);
    }
}
