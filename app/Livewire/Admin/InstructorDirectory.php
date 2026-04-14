<?php

namespace App\Livewire\Admin;

use App\Exports\InstructorTemplateExport;
use App\Exports\InstructorsExport;
use App\Imports\InstructorsImport;
use App\Livewire\Concerns\HasImportExport;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class InstructorDirectory extends Component
{
    use WithPagination, WithFileUploads, HasImportExport;

    // Search & Filters
    public string $search = '';
    public string $sortBy = 'latest';
    public string $filterGender = '';
    public string $filterSpecialization = '';

    // CRUD Modal
    public bool $showForm = false;
    public bool $showDeleteModal = false;
    public bool $showDetailModal = false;
    public ?int $editingInstructorId = null;
    public ?int $deletingInstructorId = null;
    public ?int $viewingInstructorId = null;

    // Form fields
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $nip = '';
    public string $phone = '';
    public string $address = '';
    public string $gender = '';
    public ?string $birth_date = null;
    public string $specialization = '';
    public string $bio = '';

    protected $queryString = ['search', 'sortBy', 'filterGender', 'filterSpecialization'];

    public function rules(): array
    {
        $emailRule = 'required|email|unique:users,email';
        $nipRule = 'nullable|unique:users,nip';

        if ($this->editingInstructorId) {
            $emailRule .= ',' . $this->editingInstructorId;
            $nipRule .= ',' . $this->editingInstructorId;
        }

        return [
            'name' => 'required|min:2|max:255',
            'email' => $emailRule,
            'password' => $this->editingInstructorId ? 'nullable|min:6' : 'required|min:6',
            'nip' => $nipRule,
            'phone' => 'nullable|max:20',
            'address' => 'nullable|max:500',
            'gender' => 'nullable|in:L,P',
            'birth_date' => 'nullable|date',
            'specialization' => 'nullable|max:255',
            'bio' => 'nullable|max:1000',
        ];
    }

    protected $validationAttributes = [
        'name' => 'Nama Lengkap',
        'email' => 'Email',
        'password' => 'Password',
        'nip' => 'NIP',
        'phone' => 'No. Telepon',
        'address' => 'Alamat',
        'gender' => 'Jenis Kelamin',
        'birth_date' => 'Tanggal Lahir',
        'specialization' => 'Bidang Keahlian',
        'bio' => 'Bio',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterGender(): void
    {
        $this->resetPage();
    }

    public function updatedFilterSpecialization(): void
    {
        $this->resetPage();
    }

    // ─── CRUD ──────────────────────────────────────────

    public function openCreateForm(): void
    {
        $this->resetFormFields();
        $this->editingInstructorId = null;
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $instructor = User::findOrFail($id);
        $this->editingInstructorId = $id;
        $this->name = $instructor->name;
        $this->email = $instructor->email;
        $this->password = '';
        $this->nip = $instructor->nip ?? '';
        $this->phone = $instructor->phone ?? '';
        $this->address = $instructor->address ?? '';
        $this->gender = $instructor->gender ?? '';
        $this->birth_date = $instructor->birth_date?->format('Y-m-d');
        $this->specialization = $instructor->specialization ?? '';
        $this->bio = $instructor->bio ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => 'instructor',
            'nip' => $this->nip ?: null,
            'phone' => $this->phone ?: null,
            'address' => $this->address ?: null,
            'gender' => $this->gender ?: null,
            'birth_date' => $this->birth_date ?: null,
            'specialization' => $this->specialization ?: null,
            'bio' => $this->bio ?: null,
        ];

        if ($this->editingInstructorId) {
            $instructor = User::findOrFail($this->editingInstructorId);
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            $instructor->update($data);
            session()->flash('success', 'Data guru berhasil diperbarui!');
        } else {
            $data['password'] = Hash::make($this->password);
            User::create($data);
            session()->flash('success', 'Guru baru berhasil ditambahkan!');
        }

        $this->showForm = false;
        $this->resetFormFields();
    }

    public function viewDetail(int $id): void
    {
        $this->viewingInstructorId = $id;
        $this->showDetailModal = true;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingInstructorId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingInstructorId) {
            User::findOrFail($this->deletingInstructorId)->delete();
            session()->flash('success', 'Guru berhasil dihapus.');
        }
        $this->showDeleteModal = false;
        $this->deletingInstructorId = null;
    }

    // ─── HasImportExport contract ─────────────────────

    protected function getExportInstance(): InstructorsExport
    {
        return new InstructorsExport($this->search, $this->filterGender, $this->filterSpecialization);
    }

    protected function getExportFilename(): string
    {
        return 'direktori-guru-' . now()->format('Y-m-d') . '.xlsx';
    }

    protected function getImportInstance(): InstructorsImport
    {
        return new InstructorsImport();
    }

    protected function entityLabel(): string
    {
        return 'guru';
    }

    public function importExcel(): void
    {
        $this->runImport();
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new InstructorTemplateExport(),
            'template-import-guru.xlsx'
        );
    }

    // ─── HELPERS ──────────────────────────────────────

    private function resetFormFields(): void
    {
        $this->reset([
            'name', 'email', 'password', 'nip', 'phone',
            'address', 'gender', 'birth_date', 'specialization', 'bio',
        ]);
    }

    public function render()
    {
        $query = User::where('role', 'instructor')
            ->withCount('instructedCourses');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('nip', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterGender) {
            $query->where('gender', $this->filterGender);
        }

        if ($this->filterSpecialization) {
            $query->where('specialization', $this->filterSpecialization);
        }

        $query->when($this->sortBy === 'latest', fn ($q) => $q->latest())
              ->when($this->sortBy === 'name', fn ($q) => $q->orderBy('name'))
              ->when($this->sortBy === 'courses', fn ($q) => $q->orderByDesc('instructed_courses_count'));

        $instructors = $query->paginate(15);

        $totalInstructors = User::where('role', 'instructor')->count();
        $activeThisMonth = User::where('role', 'instructor')
            ->whereMonth('created_at', now()->month)
            ->count();
        $specializations = User::where('role', 'instructor')
            ->whereNotNull('specialization')
            ->distinct()
            ->pluck('specialization')
            ->sort();

        $viewingInstructor = $this->viewingInstructorId
            ? User::withCount('instructedCourses')->find($this->viewingInstructorId)
            : null;

        return view('livewire.admin.instructor-directory', compact(
            'instructors', 'totalInstructors', 'activeThisMonth', 'specializations', 'viewingInstructor'
        ))->layout('layouts.admin', ['title' => 'Direktori Guru']);
    }
}
