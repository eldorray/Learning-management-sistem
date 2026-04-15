<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class ParentDirectory extends Component
{
    use WithPagination;

    // Search & Filters
    public string $search  = '';
    public string $sortBy  = 'latest';

    // CRUD Modal
    public bool $showForm        = false;
    public bool $showDeleteModal = false;
    public bool $showDetailModal = false;
    public ?int $editingParentId  = null;
    public ?int $deletingParentId = null;
    public ?int $viewingParentId  = null;

    // Form fields
    public string  $name     = '';
    public string  $email    = '';
    public string  $password = '';
    public string  $phone    = '';
    public string  $address  = '';

    protected $queryString = ['search', 'sortBy'];

    public function rules(): array
    {
        $emailRule = 'required|email|unique:users,email';
        if ($this->editingParentId) {
            $emailRule .= ',' . $this->editingParentId;
        }

        return [
            'name'     => 'required|min:2|max:255',
            'email'    => $emailRule,
            'password' => $this->editingParentId ? 'nullable|min:6' : 'required|min:6',
            'phone'    => 'nullable|max:20',
            'address'  => 'nullable|max:500',
        ];
    }

    protected $validationAttributes = [
        'name'     => 'Nama Lengkap',
        'email'    => 'Email',
        'password' => 'Password',
        'phone'    => 'No. Telepon',
        'address'  => 'Alamat',
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // ─── CRUD ─────────────────────────────────────────────

    public function openCreateForm(): void
    {
        $this->resetFormFields();
        $this->editingParentId = null;
        $this->showForm = true;
    }

    public function openEditForm(int $id): void
    {
        $parent = User::findOrFail($id);
        $this->editingParentId = $id;
        $this->name     = $parent->name;
        $this->email    = $parent->email;
        $this->password = '';
        $this->phone    = $parent->phone ?? '';
        $this->address  = $parent->address ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'    => $this->name,
            'email'   => $this->email,
            'role'    => 'parent',
            'phone'   => $this->phone ?: null,
            'address' => $this->address ?: null,
        ];

        if ($this->editingParentId) {
            $parent = User::findOrFail($this->editingParentId);
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            $parent->update($data);
            session()->flash('success', 'Data orang tua berhasil diperbarui!');
        } else {
            $data['password'] = Hash::make($this->password);
            User::create($data);
            session()->flash('success', 'Akun orang tua baru berhasil ditambahkan!');
        }

        $this->showForm = false;
        $this->resetFormFields();
    }

    public function viewDetail(int $id): void
    {
        $this->viewingParentId = $id;
        $this->showDetailModal = true;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingParentId = $id;
        $this->showDeleteModal  = true;
    }

    public function delete(): void
    {
        if ($this->deletingParentId) {
            $parent = User::findOrFail($this->deletingParentId);
            // Detach all linked children first
            $parent->children()->detach();
            $parent->delete();
            session()->flash('success', 'Akun orang tua berhasil dihapus.');
        }
        $this->showDeleteModal  = false;
        $this->deletingParentId = null;
    }

    public function unlinkChild(int $parentId, int $childId): void
    {
        $parent = User::find($parentId);
        if ($parent) {
            $parent->children()->detach($childId);
        }
        // Refresh detail modal
        $this->viewingParentId = $parentId;
    }

    // ─── HELPERS ──────────────────────────────────────────

    private function resetFormFields(): void
    {
        $this->reset(['name', 'email', 'password', 'phone', 'address']);
    }

    public function render()
    {
        $query = User::where('role', 'parent')
            ->withCount('children');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        $query->when($this->sortBy === 'latest', fn ($q) => $q->latest())
              ->when($this->sortBy === 'name', fn ($q) => $q->orderBy('name'))
              ->when($this->sortBy === 'children', fn ($q) => $q->orderByDesc('children_count'));

        $parents      = $query->paginate(15);
        $totalParents = User::where('role', 'parent')->count();
        $linkedCount  = User::where('role', 'parent')->has('children')->count();

        $viewingParent = $this->viewingParentId
            ? User::with(['children' => fn ($q) => $q->select('users.id', 'name', 'email', 'class_group', 'nis')->withPivot('hubungan')])
                  ->find($this->viewingParentId)
            : null;

        return view('livewire.admin.parent-directory', compact(
            'parents', 'totalParents', 'linkedCount', 'viewingParent'
        ))->layout('layouts.admin', ['title' => 'Direktori Orang Tua']);
    }
}
