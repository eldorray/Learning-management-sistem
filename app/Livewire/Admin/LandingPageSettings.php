<?php

namespace App\Livewire\Admin;

use App\Support\LandingPageContent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class LandingPageSettings extends Component
{
    use WithFileUploads;

    public array $texts = [];

    public array $uploads = [];

    public function mount(): void
    {
        $this->authorizeAdmin();
        $values = LandingPageContent::values();

        foreach (LandingPageContent::schema() as $key => $field) {
            if (in_array($field['type'], ['text', 'link', 'select'], true)) {
                $this->texts[$key] = $values[$key];
            }
        }
    }

    public function save(): void
    {
        $this->authorizeAdmin();
        $rules = [];
        foreach (LandingPageContent::schema() as $key => $field) {
            if ($field['type'] === 'image') {
                $rules["uploads.$key"] = ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096', 'dimensions:min_width=1,min_height=1'];
            }
            if (in_array($field['type'], ['text', 'link', 'select'], true)) {
                $rules["texts.$key"] = ['present', 'nullable', 'string', $key === 'js_wordmark' ? 'max:12' : 'max:2000'];
                if ($field['type'] === 'link') {
                    $rules["texts.$key"] = ['required', 'string', 'regex:~\A(?:\#(?:top|hero|gate|pathways|lessons|eternity)|/(?:login|register))\z~'];
                }
                if ($key === 'js_wordmark') {
                    $rules["texts.$key"][] = 'required';
                }
                if ($key === 'language') {
                    $rules["texts.$key"] = ['required', 'string', 'regex:/^[a-z]{2,3}(-[A-Za-z]{2,4})?$/D'];
                }
            }
        }
        $this->validate($rules);
        $values = LandingPageContent::values();

        foreach (LandingPageContent::schema() as $key => $field) {
            if (in_array($field['type'], ['text', 'link', 'select'], true) && array_key_exists($key, $this->texts)) {
                $values[$key] = $this->texts[$key];
            }
            if ($field['type'] === 'image' && ($this->uploads[$key] ?? null) instanceof TemporaryUploadedFile) {
                $values[$key] = '/storage/'.$this->uploads[$key]->store('landing', 'public');
            }
        }

        try {
            LandingPageContent::save($values);
        } catch (ValidationException $exception) {
            $errors = [];
            foreach ($exception->errors() as $key => $messages) {
                $errors[($key === 'background_image' ? 'uploads.' : 'texts.').$key] = $messages;
            }
            throw ValidationException::withMessages($errors);
        }
        $this->uploads = [];
        session()->flash('success', 'Perubahan landing page berhasil disimpan.');
    }

    public function resetImage(string $key): void
    {
        $this->authorizeAdmin();
        $field = LandingPageContent::schema()[$key] ?? null;
        abort_unless($field && $field['type'] === 'image', 404);

        $reset = [$key => $field['default']];
        if ($key === 'background_image') {
            $reset['background_mode'] = '3d';
        }
        LandingPageContent::save($reset);
        if ($key === 'background_image') {
            $this->texts['background_mode'] = '3d';
        }
        unset($this->uploads[$key]);
        $this->resetValidation('uploads.'.$key);
        session()->flash('success', 'Gambar bawaan berhasil dipulihkan.');
    }

    public function render()
    {
        $this->authorizeAdmin();

        $previews = [];
        foreach (LandingPageContent::schema() as $key => $field) {
            $upload = $this->uploads[$key] ?? null;
            if ($field['type'] === 'image' && $upload instanceof TemporaryUploadedFile
                && Validator::make(['image' => $upload], ['image' => ['image', 'mimes:png,jpg,jpeg,webp', 'max:4096', 'dimensions:min_width=1,min_height=1']])->passes()) {
                $previews[$key] = $upload->temporaryUrl();
            }
        }

        return view('livewire.admin.landing-page-settings', [
            'previews' => $previews,
            'sections' => collect(LandingPageContent::schema())->groupBy('section', preserveKeys: true)->sortBy(fn ($fields, $section) => $section === 'Latar gedung sekolah' ? 0 : 1),
            'values' => LandingPageContent::values(),
        ])->layout('layouts.admin', ['title' => 'Pengaturan Landing Page']);
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->fresh()?->isAdmin(), 403);
    }
}
