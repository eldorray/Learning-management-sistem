<?php

namespace App\Livewire\Admin;

use App\Support\SketchbookContent;
use App\Support\SketchbookSpread;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class SketchbookSettings extends Component
{
    use WithFileUploads;

    private const UPLOAD_RULES = ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096', 'dimensions:min_width=1,min_height=1'];

    public array $texts = [];

    public array $uploads = [];

    public function mount(): void
    {
        $this->authorizeAdmin();
        $values = SketchbookContent::values();
        foreach (SketchbookContent::schema() as $key => $field) {
            if ($field['type'] !== 'image') {
                $this->texts[$key] = $values[$key];
            }
        }
    }

    public function save(): void
    {
        $this->authorizeAdmin();
        $rules = [];
        foreach (SketchbookContent::rules() as $key => $keyRules) {
            if (SketchbookContent::schema()[$key]['type'] === 'image') {
                $rules["uploads.$key"] = self::UPLOAD_RULES;
            } else {
                $rules["texts.$key"] = ['present', ...$keyRules];
            }
        }
        $this->validate($rules);

        $values = [];
        foreach (SketchbookContent::schema() as $key => $field) {
            if ($field['type'] !== 'image') {
                $values[$key] = $this->texts[$key];
            } elseif (($this->uploads[$key] ?? null) instanceof TemporaryUploadedFile) {
                $values[$key] = '/storage/'.$this->uploads[$key]->store('landing', 'public');
                SketchbookSpread::url($values[$key]);
            }
        }
        SketchbookContent::save($values);

        $this->uploads = [];
        session()->flash('success', 'Perubahan landing page berhasil disimpan.');
    }

    public function resetImage(string $key): void
    {
        $this->authorizeAdmin();
        $field = SketchbookContent::schema()[$key] ?? null;
        abort_unless($field && $field['type'] === 'image', 404);

        SketchbookContent::save([$key => '']);
        unset($this->uploads[$key]);
        $this->resetValidation('uploads.'.$key);
        session()->flash('success', 'Ilustrasi bawaan berhasil dipulihkan.');
    }

    public function render()
    {
        $this->authorizeAdmin();

        $previews = [];
        foreach ($this->uploads as $key => $upload) {
            if ($upload instanceof TemporaryUploadedFile && Validator::make(['image' => $upload], ['image' => self::UPLOAD_RULES])->passes()) {
                $previews[$key] = $upload->temporaryUrl();
            }
        }

        return view('livewire.admin.sketchbook-settings', [
            'previews' => $previews,
            'sections' => collect(SketchbookContent::schema())->groupBy('section', preserveKeys: true),
            'values' => SketchbookContent::values(),
        ])->layout('layouts.admin', ['title' => 'Pengaturan Landing Page']);
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->fresh()?->isAdmin(), 403);
    }
}
