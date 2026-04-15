<?php

namespace App\Livewire\Admin;

use App\Exports\StudentsExport;
use App\Exports\StudentTemplateExport;
use App\Imports\StudentsImport;
use App\Livewire\Concerns\HasImportExport;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class StudentDirectory extends Component
{
    use WithPagination, WithFileUploads, HasImportExport;

    // Search & Filters
    public string $search = '';
    public string $sortBy = 'latest';
    public string $filterGender = '';
    public string $filterClass = '';

    // CRUD Modal
    public bool $showForm = false;
    public bool $showDeleteModal = false;
    public bool $showDetailModal = false;
    public bool $showParentModal = false;
    public ?int $editingStudentId = null;
    public ?int $deletingStudentId = null;
    public ?int $viewingStudentId = null;
    public ?int $parentLinkStudentId = null;

    // Parent linking
    public string $parentEmail    = '';
    public string $parentHubungan = 'orang_tua';

    // Form fields
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $nis = '';
    public string $nisn = '';
    public string $phone = '';
    public string $address = '';
    public string $gender = '';
    public ?string $birth_date = null;
    public string $guardian_name = '';
    public string $class_group = '';

    protected $queryString = ['search', 'sortBy', 'filterGender', 'filterClass'];

    public function rules(): array
    {
        $emailRule = 'required|email|unique:users,email';
        $nisRule = 'nullable|unique:users,nis';
        $nisnRule = 'nullable|digits:10|unique:users,nisn';

        if ($this->editingStudentId) {
            $emailRule .= ',' . $this->editingStudentId;
            $nisRule .= ',' . $this->editingStudentId;
            $nisnRule .= ',' . $this->editingStudentId;
        }

        return [
            'name' => 'required|min:2|max:255',
            'email' => $emailRule,
            'password' => $this->editingStudentId ? 'nullable|min:6' : 'required|min:6',
            'nis' => $nisRule,
            'nisn' => $nisnRule,
            'phone' => 'nullable|max:20',
            'address' => 'nullable|max:500',
            'gender' => 'nullable|in:L,P',
            'birth_date' => 'nullable|date',
            'guardian_name' => 'nullable|max:255',
            'class_group' => 'nullable|max:50',
        ];
    }

    protected $validationAttributes = [
        'name' => 'Nama Lengkap',
        'email' => 'Email',
        'password' => 'Password',
        'nis' => 'NIS',
        'nisn' => 'NISN',
        'phone' => 'No. Telepon',
        'address' => 'Alamat',
        'gender' => 'Jenis Kelamin',
        'birth_date' => 'Tanggal Lahir',
        'guardian_name' => 'Nama Wali',
        'class_group' => 'Kelas',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterGender(): void
    {
        $this->resetPage();
    }

    public function updatedFilterClass(): void
    {
        $this->resetPage();
    }

    /**
     * Get student IDs enrolled in instructor's courses (for scoping).
     */
    private function getInstructorStudentIds(): ?array
    {
        $user = auth()->user();
        if (!$user->isInstructor()) {
            return null; // Admin sees all
        }

        $courseIds = $user->instructedCourses()->pluck('id');
        return Enrollment::whereIn('course_id', $courseIds)
            ->distinct()
            ->pluck('user_id')
            ->toArray();
    }

    // ─── CRUD ──────────────────────────────────────────

    public function openCreateForm(): void
    {
        if (auth()->user()->isInstructor()) return; // Instructors can't create students

        $this->resetFormFields();
        $this->editingStudentId = null;
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        if (auth()->user()->isInstructor()) return;

        $student = User::findOrFail($id);
        $this->editingStudentId = $id;
        $this->name = $student->name;
        $this->email = $student->email;
        $this->password = '';
        $this->nis = $student->nis ?? '';
        $this->nisn = $student->nisn ?? '';
        $this->phone = $student->phone ?? '';
        $this->address = $student->address ?? '';
        $this->gender = $student->gender ?? '';
        $this->birth_date = $student->birth_date?->format('Y-m-d');
        $this->guardian_name = $student->guardian_name ?? '';
        $this->class_group = $student->class_group ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        if (auth()->user()->isInstructor()) return;

        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => 'student',
            'nis' => $this->nis ?: null,
            'nisn' => $this->nisn ?: null,
            'phone' => $this->phone ?: null,
            'address' => $this->address ?: null,
            'gender' => $this->gender ?: null,
            'birth_date' => $this->birth_date ?: null,
            'guardian_name' => $this->guardian_name ?: null,
            'class_group' => $this->class_group ?: null,
        ];

        if ($this->editingStudentId) {
            $student = User::findOrFail($this->editingStudentId);
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            $student->update($data);
            session()->flash('success', 'Data siswa berhasil diperbarui!');
        } else {
            $data['password'] = Hash::make($this->password);
            User::create($data);
            session()->flash('success', 'Siswa baru berhasil ditambahkan!');
        }

        $this->showForm = false;
        $this->resetFormFields();
    }

    public function viewDetail(int $id): void
    {
        $this->viewingStudentId = $id;
        $this->showDetailModal = true;
    }

    public function confirmDelete(int $id): void
    {
        if (auth()->user()->isInstructor()) return;

        $this->deletingStudentId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if (auth()->user()->isInstructor()) return;

        if ($this->deletingStudentId) {
            User::findOrFail($this->deletingStudentId)->delete();
            session()->flash('success', 'Siswa berhasil dihapus.');
        }
        $this->showDeleteModal = false;
        $this->deletingStudentId = null;
    }

    // ─── PARENT LINKING ───────────────────────────────

    public function openParentModal(int $studentId): void
    {
        $this->parentLinkStudentId = $studentId;
        $this->parentEmail         = '';
        $this->parentHubungan      = 'orang_tua';
        $this->showParentModal     = true;
    }

    public function linkParent(): void
    {
        $this->validate([
            'parentEmail'    => 'required|email|exists:users,email',
            'parentHubungan' => 'required|in:orang_tua,wali',
        ]);

        $parent = User::where('email', $this->parentEmail)
            ->where('role', 'parent')
            ->first();

        if (!$parent) {
            $this->addError('parentEmail', 'Email tidak ditemukan atau bukan akun Orang Tua.');
            return;
        }

        $parent->children()->syncWithoutDetaching([
            $this->parentLinkStudentId => ['hubungan' => $this->parentHubungan],
        ]);

        $this->showParentModal = false;
        session()->flash('success', 'Orang tua berhasil dihubungkan ke siswa.');
    }

    public function unlinkParent(int $studentId, int $parentId): void
    {
        $parent = User::find($parentId);
        if ($parent) {
            $parent->children()->detach($studentId);
        }
        session()->flash('success', 'Relasi orang tua dihapus.');
    }

    // ─── IMPORT / EXPORT ──────────────────────────────

    // ─── HasImportExport contract ─────────────────────

    protected function getExportInstance(): StudentsExport
    {
        return new StudentsExport($this->search, $this->filterGender, $this->filterClass);
    }

    protected function getExportFilename(): string
    {
        return 'direktori-siswa-' . now()->format('Y-m-d') . '.xlsx';
    }

    protected function getImportInstance(): StudentsImport
    {
        return new StudentsImport();
    }

    protected function entityLabel(): string
    {
        return 'siswa';
    }

    public function openImportModal(): void
    {
        if (auth()->user()->isInstructor()) return;

        $this->importFile       = null;
        $this->importResult     = '';
        $this->importResultType = '';
        $this->showImportModal  = true;
    }

    public function importExcel(): void
    {
        if (auth()->user()->isInstructor()) return;
        $this->runImport();
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new StudentTemplateExport(),
            'template-import-siswa.xlsx'
        );
    }

    // ─── HELPERS ──────────────────────────────────────

    private function resetFormFields(): void
    {
        $this->reset([
            'name', 'email', 'password', 'nis', 'nisn', 'phone',
            'address', 'gender', 'birth_date', 'guardian_name', 'class_group',
        ]);
    }

    public function render()
    {
        $user = auth()->user();
        $isInstructor = $user->isInstructor();
        $scopedStudentIds = $this->getInstructorStudentIds();

        $query = User::where('role', 'student')
            ->withCount(['enrollments', 'enrollments as completed_count' => fn ($q) => $q->where('status', 'completed')]);

        // Instructor: only students in their courses
        if ($scopedStudentIds !== null) {
            $query->whereIn('id', $scopedStudentIds);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('nis', 'like', '%' . $this->search . '%')
                  ->orWhere('nisn', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterGender) {
            $query->where('gender', $this->filterGender);
        }

        if ($this->filterClass) {
            $query->where('class_group', $this->filterClass);
        }

        $query->when($this->sortBy === 'latest', fn ($q) => $q->latest())
              ->when($this->sortBy === 'name', fn ($q) => $q->orderBy('name'))
              ->when($this->sortBy === 'xp', fn ($q) => $q->orderByDesc('xp_points'));

        $students = $query->paginate(15);

        // Scoped totals
        if ($scopedStudentIds !== null) {
            $totalStudents = count($scopedStudentIds);
            $activeThisMonth = User::where('role', 'student')
                ->whereIn('id', $scopedStudentIds)
                ->whereMonth('created_at', now()->month)
                ->count();
            $classGroups = User::where('role', 'student')
                ->whereIn('id', $scopedStudentIds)
                ->whereNotNull('class_group')
                ->distinct()
                ->pluck('class_group')
                ->sort();
        } else {
            $totalStudents = User::where('role', 'student')->count();
            $activeThisMonth = User::where('role', 'student')
                ->whereMonth('created_at', now()->month)
                ->count();
            $classGroups = User::where('role', 'student')
                ->whereNotNull('class_group')
                ->distinct()
                ->pluck('class_group')
                ->sort();
        }

        $viewingStudent = $this->viewingStudentId
            ? User::withCount(['enrollments', 'enrollments as completed_count' => fn ($q) => $q->where('status', 'completed')])
                  ->with('parents')
                  ->find($this->viewingStudentId)
            : null;

        $parentLinkStudent = $this->parentLinkStudentId
            ? User::with('parents')->find($this->parentLinkStudentId)
            : null;

        return view('livewire.admin.student-directory', compact(
            'students', 'totalStudents', 'activeThisMonth', 'classGroups',
            'viewingStudent', 'isInstructor', 'parentLinkStudent'
        ))->layout('layouts.admin', ['title' => 'Direktori Siswa']);
    }
}
