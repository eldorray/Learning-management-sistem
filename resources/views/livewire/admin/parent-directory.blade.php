<div class="px-4 md:px-8 py-6 space-y-6">

    {{-- ── Header ────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-headline font-extrabold text-2xl md:text-3xl text-[#2c2f31]">Direktori Orang Tua</h1>
            <p class="text-sm text-[#595c5e] mt-1">Kelola akun orang tua / wali siswa</p>
        </div>
        <button wire:click="openCreateForm"
            class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full shadow-sm hover:scale-[1.02] transition-transform text-sm">
            <span class="material-symbols-outlined text-sm">person_add</span>
            Tambah Orang Tua
        </button>
    </div>

    {{-- ── Flash Message ──────────────────────────────────────── --}}
    @if (session()->has('success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-[#73f2dd]/30 border border-[#73f2dd]/40 text-[#00675c] rounded-xl text-sm font-medium">
            <span class="material-symbols-outlined text-sm">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Stats Cards ────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-[#abadaf]/10 shadow-sm">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-9 h-9 bg-amber-50 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-600 text-sm">supervisor_account</span>
                </div>
                <p class="text-xs text-[#595c5e] font-medium">Total Orang Tua</p>
            </div>
            <p class="text-2xl font-headline font-extrabold text-[#2c2f31]">{{ $totalParents }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-[#abadaf]/10 shadow-sm">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#0058ba] text-sm">link</span>
                </div>
                <p class="text-xs text-[#595c5e] font-medium">Terhubung ke Siswa</p>
            </div>
            <p class="text-2xl font-headline font-extrabold text-[#0058ba]">{{ $linkedCount }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-[#abadaf]/10 shadow-sm col-span-2 md:col-span-1">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-9 h-9 bg-red-50 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-[#b31b25] text-sm">link_off</span>
                </div>
                <p class="text-xs text-[#595c5e] font-medium">Belum Terhubung</p>
            </div>
            <p class="text-2xl font-headline font-extrabold text-[#b31b25]">{{ $totalParents - $linkedCount }}</p>
        </div>
    </div>

    {{-- ── Search & Sort ──────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 border-b border-[#f5f7f9] flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#595c5e] text-sm">search</span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama, email, telepon..."
                    class="w-full pl-9 pr-4 py-2.5 bg-[#eef1f3] rounded-xl border border-transparent focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm placeholder-[#abadaf]">
            </div>
            <select wire:model.live="sortBy"
                class="px-4 py-2.5 bg-[#eef1f3] rounded-xl border border-transparent focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm text-[#2c2f31]">
                <option value="latest">Terbaru</option>
                <option value="name">Nama A–Z</option>
                <option value="children">Jumlah Anak</option>
            </select>
        </div>

        {{-- ── Table ─────────────────────────────────────────── --}}
        @if ($parents->isEmpty())
            <div class="py-16 text-center">
                <div class="w-16 h-16 bg-[#eef1f3] rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[#abadaf] text-3xl">supervisor_account</span>
                </div>
                <p class="text-[#595c5e] font-medium">Belum ada data orang tua</p>
                <p class="text-sm text-[#abadaf] mt-1">{{ $search ? 'Coba kata kunci lain.' : 'Klik "Tambah Orang Tua" untuk memulai.' }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-[#f5f7f9]">
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-[#595c5e] uppercase tracking-wide">Orang Tua</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-[#595c5e] uppercase tracking-wide hidden md:table-cell">Telepon</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-[#595c5e] uppercase tracking-wide">Anak Terhubung</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-[#595c5e] uppercase tracking-wide hidden lg:table-cell">Bergabung</th>
                            <th class="px-4 md:px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f5f7f9]">
                        @foreach ($parents as $parent)
                            <tr class="hover:bg-[#f5f7f9]/60 transition-colors group">
                                <td class="px-4 md:px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $parent->avatar_url }}" alt="{{ $parent->name }}"
                                            class="w-9 h-9 rounded-full object-cover flex-shrink-0">
                                        <div>
                                            <p class="text-sm font-bold text-[#2c2f31]">{{ $parent->name }}</p>
                                            <p class="text-xs text-[#595c5e]">{{ $parent->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 py-3 hidden md:table-cell">
                                    <span class="text-sm text-[#595c5e]">{{ $parent->phone ?? '-' }}</span>
                                </td>
                                <td class="px-4 md:px-6 py-3">
                                    @if ($parent->children_count > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-[#0058ba] text-xs font-bold rounded-full">
                                            <span class="material-symbols-outlined text-xs" style="font-size:13px">group</span>
                                            {{ $parent->children_count }} anak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#eef1f3] text-[#abadaf] text-xs font-medium rounded-full">
                                            Belum terhubung
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 md:px-6 py-3 hidden lg:table-cell">
                                    <span class="text-sm text-[#595c5e]">{{ $parent->created_at->format('d M Y') }}</span>
                                </td>
                                <td class="px-4 md:px-6 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <button wire:click="viewDetail({{ $parent->id }})"
                                            class="p-1.5 text-[#595c5e] hover:text-[#0058ba] hover:bg-blue-50 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-sm">visibility</span>
                                        </button>
                                        <button wire:click="openEditForm({{ $parent->id }})"
                                            class="p-1.5 text-[#595c5e] hover:text-[#0058ba] hover:bg-blue-50 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-sm">edit</span>
                                        </button>
                                        <button wire:click="confirmDelete({{ $parent->id }})"
                                            class="p-1.5 text-[#595c5e] hover:text-[#b31b25] hover:bg-red-50 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-sm">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 md:px-6 py-4 border-t border-[#f5f7f9]">
                {{ $parents->links() }}
            </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════════════════════
         CREATE / EDIT MODAL
         ════════════════════════════════════════════════════════ --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4"
            wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-2xl w-full sm:max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-[#eef1f3] flex items-center justify-between sticky top-0 bg-white z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-sm">{{ $editingParentId ? 'edit' : 'person_add' }}</span>
                        </div>
                        <h3 class="font-headline font-bold text-xl text-[#2c2f31]">
                            {{ $editingParentId ? 'Edit Orang Tua' : 'Tambah Orang Tua' }}
                        </h3>
                    </div>
                    <button wire:click="$set('showForm', false)" class="p-2 rounded-full hover:bg-[#eef1f3] transition-colors">
                        <span class="material-symbols-outlined text-[#595c5e]">close</span>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Nama Lengkap <span class="text-[#b31b25]">*</span></label>
                        <input type="text" wire:model="name" placeholder="Nama orang tua / wali"
                            class="w-full px-4 py-3 bg-[#eef1f3] rounded-xl border border-transparent focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm placeholder-[#abadaf]">
                        @error('name') <p class="text-[#b31b25] text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Email <span class="text-[#b31b25]">*</span></label>
                        <input type="email" wire:model="email" placeholder="email@example.com"
                            class="w-full px-4 py-3 bg-[#eef1f3] rounded-xl border border-transparent focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm placeholder-[#abadaf]">
                        @error('email') <p class="text-[#b31b25] text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">
                            Password {{ $editingParentId ? '(kosongkan jika tidak diubah)' : '*' }}
                        </label>
                        <input type="password" wire:model="password" placeholder="Min. 6 karakter"
                            class="w-full px-4 py-3 bg-[#eef1f3] rounded-xl border border-transparent focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm placeholder-[#abadaf]">
                        @error('password') <p class="text-[#b31b25] text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    {{-- Telepon --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">No. Telepon</label>
                        <input type="text" wire:model="phone" placeholder="08xx-xxxx-xxxx"
                            class="w-full px-4 py-3 bg-[#eef1f3] rounded-xl border border-transparent focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm placeholder-[#abadaf]">
                        @error('phone') <p class="text-[#b31b25] text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    {{-- Alamat --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Alamat</label>
                        <textarea wire:model="address" placeholder="Alamat lengkap" rows="3"
                            class="w-full px-4 py-3 bg-[#eef1f3] rounded-xl border border-transparent focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm placeholder-[#abadaf] resize-none"></textarea>
                        @error('address') <p class="text-[#b31b25] text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button wire:click="$set('showForm', false)"
                            class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors">
                            Batal
                        </button>
                        <button wire:click="save"
                            class="flex-1 py-3 bg-gradient-to-br from-amber-500 to-amber-600 text-white font-bold rounded-full hover:scale-[1.01] transition-transform shadow-sm flex items-center justify-center gap-2">
                            <span wire:loading wire:target="save" class="inline-block animate-spin">⟳</span>
                            <span class="material-symbols-outlined text-sm" wire:loading.remove wire:target="save">save</span>
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════
         DETAIL MODAL
         ════════════════════════════════════════════════════════ --}}
    @if ($showDetailModal && $viewingParent)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4"
            wire:click.self="$set('showDetailModal', false)">
            <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-2xl w-full sm:max-w-md max-h-[90vh] overflow-y-auto">
                {{-- Top banner --}}
                <div class="h-20 bg-gradient-to-br from-amber-400 to-amber-600 rounded-t-2xl sm:rounded-t-xl relative">
                    <button wire:click="$set('showDetailModal', false)"
                        class="absolute top-3 right-3 p-1.5 bg-black/20 hover:bg-black/30 rounded-full transition-colors">
                        <span class="material-symbols-outlined text-white text-sm">close</span>
                    </button>
                </div>

                {{-- Avatar --}}
                <div class="px-6 -mt-10 mb-3">
                    <img src="{{ $viewingParent->avatar_url }}" alt="{{ $viewingParent->name }}"
                        class="w-20 h-20 rounded-2xl object-cover border-4 border-white shadow-md">
                </div>

                <div class="px-6 pb-2">
                    <h3 class="font-headline font-extrabold text-xl text-[#2c2f31]">{{ $viewingParent->name }}</h3>
                    <p class="text-sm text-[#595c5e]">{{ $viewingParent->email }}</p>
                    <span class="inline-block mt-2 px-3 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-full">Orang Tua / Wali</span>
                </div>

                <div class="px-6 pb-6 space-y-3 mt-4">
                    <div class="flex items-center gap-3 py-2.5 border-b border-[#f5f7f9]">
                        <span class="material-symbols-outlined text-[#595c5e] text-sm">call</span>
                        <span class="text-sm text-[#595c5e] w-28 shrink-0">Telepon</span>
                        <span class="text-sm font-semibold text-[#2c2f31]">{{ $viewingParent->phone ?? '-' }}</span>
                    </div>
                    <div class="flex items-start gap-3 py-2.5 border-b border-[#f5f7f9]">
                        <span class="material-symbols-outlined text-[#595c5e] text-sm mt-0.5">location_on</span>
                        <span class="text-sm text-[#595c5e] w-28 shrink-0">Alamat</span>
                        <span class="text-sm font-semibold text-[#2c2f31]">{{ $viewingParent->address ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-3 py-2.5 border-b border-[#f5f7f9]">
                        <span class="material-symbols-outlined text-[#595c5e] text-sm">calendar_today</span>
                        <span class="text-sm text-[#595c5e] w-28 shrink-0">Bergabung</span>
                        <span class="text-sm font-semibold text-[#2c2f31]">{{ $viewingParent->created_at->format('d F Y') }}</span>
                    </div>

                    {{-- Daftar anak terhubung --}}
                    <div class="pt-2">
                        <p class="text-xs font-semibold text-[#595c5e] uppercase tracking-wide mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">group</span>
                            Siswa Terhubung ({{ $viewingParent->children->count() }})
                        </p>
                        @if ($viewingParent->children->isEmpty())
                            <div class="bg-[#eef1f3] rounded-xl px-4 py-3 text-sm text-[#595c5e] text-center">
                                Belum ada siswa yang terhubung.
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach ($viewingParent->children as $child)
                                    <div class="flex items-center justify-between bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $child->avatar_url }}" alt="{{ $child->name }}"
                                                class="w-8 h-8 rounded-full object-cover">
                                            <div>
                                                <p class="text-sm font-bold text-[#2c2f31]">{{ $child->name }}</p>
                                                <p class="text-xs text-[#595c5e]">
                                                    {{ $child->class_group ?? 'Kelas -' }}
                                                    @if ($child->nis) · NIS {{ $child->nis }} @endif
                                                    · <span class="font-medium">{{ $child->pivot->hubungan === 'wali' ? 'Wali' : 'Orang Tua' }}</span>
                                                </p>
                                            </div>
                                        </div>
                                        <button wire:click="unlinkChild({{ $viewingParent->id }}, {{ $child->id }})"
                                            class="p-1.5 text-[#b31b25] hover:bg-red-50 rounded-lg transition-colors"
                                            title="Putus relasi">
                                            <span class="material-symbols-outlined text-sm">link_off</span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button wire:click="$set('showDetailModal', false)"
                            class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors">
                            Tutup
                        </button>
                        <button wire:click="openEditForm({{ $viewingParent->id }}); $set('showDetailModal', false)"
                            class="flex-1 py-3 bg-gradient-to-br from-amber-500 to-amber-600 text-white font-bold rounded-full hover:scale-[1.01] transition-transform shadow-sm flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-sm">edit</span>
                            Edit Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════
         DELETE CONFIRMATION MODAL
         ════════════════════════════════════════════════════════ --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
            wire:click.self="$set('showDeleteModal', false)">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-8 mx-4 text-center">
                <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[#b31b25] text-3xl">person_remove</span>
                </div>
                <h3 class="font-headline font-bold text-xl text-[#2c2f31] mb-2">Hapus Orang Tua?</h3>
                <p class="text-[#595c5e] text-sm mb-6">Akun akan dihapus permanen dan semua relasi dengan siswa akan diputus.</p>
                <div class="flex gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                        class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors">Batal</button>
                    <button wire:click="delete"
                        class="flex-1 py-3 bg-[#b31b25] text-white font-bold rounded-full hover:bg-[#9f0519] transition-colors">Hapus</button>
                </div>
            </div>
        </div>
    @endif

</div>
