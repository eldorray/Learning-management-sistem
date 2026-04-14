<div class="p-4 md:p-8 max-w-7xl mx-auto space-y-6 md:space-y-8">

    <!-- Header -->
    <section class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs block mb-2">{{ $isInstructor ? 'Instructor Panel' : 'Admin Panel' }}</span>
            <h2 class="text-2xl md:text-4xl font-headline font-extrabold tracking-tight text-[#2c2f31]">Direktori Siswa</h2>
            <p class="text-[#595c5e] mt-1 text-sm">
                {{ $isInstructor ? 'Siswa yang terdaftar di kursus Anda.' : 'Kelola dan pantau seluruh siswa terdaftar.' }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="bg-white px-4 py-2.5 rounded-xl border border-[#abadaf]/10 shadow-sm">
                <p class="text-xs text-[#595c5e]">Total Siswa</p>
                <p class="text-lg font-headline font-bold text-[#0058ba]">{{ $totalStudents }}</p>
            </div>
            <div class="bg-white px-4 py-2.5 rounded-xl border border-[#abadaf]/10 shadow-sm">
                <p class="text-xs text-[#595c5e]">Baru Bulan Ini</p>
                <p class="text-lg font-headline font-bold text-[#00675c]">+{{ $activeThisMonth }}</p>
            </div>
        </div>
    </section>

    <!-- Flash Message -->
    @if (session('success'))
        <div
            class="bg-[#73f2dd]/30 border border-[#00675c]/20 text-[#00675c] px-4 py-3 rounded-xl flex items-center gap-3 animate-fade-in">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <!-- Action Buttons & Filters -->
    <div class="flex flex-col gap-3">
        {{-- Action Buttons --}}
        <div class="flex items-center gap-2 flex-wrap">
            @if (!$isInstructor)
                <button wire:click="openCreateForm"
                    class="px-4 py-2.5 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.02] transition-transform shadow-lg shadow-blue-500/20 flex items-center gap-1.5 text-sm">
                    <span class="material-symbols-outlined text-sm">person_add</span>
                    <span class="hidden sm:inline">Tambah Siswa</span>
                    <span class="sm:hidden">Tambah</span>
                </button>
                <button wire:click="openImportModal"
                    class="px-4 py-2.5 bg-white border border-[#abadaf]/20 text-[#2c2f31] font-bold rounded-full hover:bg-[#eef1f3] transition-colors flex items-center gap-1.5 shadow-sm text-sm">
                    <span class="material-symbols-outlined text-sm">upload_file</span>
                    <span class="hidden sm:inline">Import</span>
                </button>
            @endif
            <button wire:click="exportExcel"
                class="px-4 py-2.5 bg-white border border-[#abadaf]/20 text-[#2c2f31] font-bold rounded-full hover:bg-[#eef1f3] transition-colors flex items-center gap-1.5 shadow-sm text-sm">
                <span class="material-symbols-outlined text-sm">download</span>
                <span class="hidden sm:inline">Export</span>
            </button>
        </div>

        {{-- Search & Filters --}}
        <div class="flex flex-col gap-2">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#595c5e] text-sm">search</span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama, email, atau NIS siswa..."
                    class="w-full pl-12 pr-4 py-2.5 bg-white border border-[#abadaf]/20 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
            </div>
            <div class="flex gap-2 flex-wrap">
                <select wire:model.live="filterGender"
                    class="flex-1 min-w-[110px] px-3 py-2.5 bg-white border border-[#abadaf]/20 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                    <option value="">Semua Gender</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
                <select wire:model.live="filterClass"
                    class="flex-1 min-w-[110px] px-3 py-2.5 bg-white border border-[#abadaf]/20 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                    <option value="">Semua Kelas</option>
                    @foreach ($classGroups as $cg)
                        <option value="{{ $cg }}">{{ $cg }}</option>
                    @endforeach
                </select>
                <select wire:model.live="sortBy"
                    class="flex-1 min-w-[110px] px-3 py-2.5 bg-white border border-[#abadaf]/20 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                    <option value="latest">Terbaru</option>
                    <option value="name">Nama A-Z</option>
                    <option value="xp">XP Tertinggi</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Results Count -->
    <div class="flex items-center justify-between">
        <p class="text-[#595c5e] text-sm">
            <span class="font-bold text-[#2c2f31]">{{ $students->total() }}</span> siswa ditemukan
        </p>
        <div wire:loading class="flex items-center gap-2 text-[#595c5e] text-sm">
            <div class="w-4 h-4 border-2 border-[#0058ba] border-t-transparent rounded-full animate-spin"></div>
            Memuat...
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
        @if ($students->isEmpty())
            <div class="p-12 md:p-16 text-center">
                <div class="w-16 h-16 bg-[#eef1f3] rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[#595c5e] text-3xl">person_search</span>
                </div>
                <h3 class="font-headline font-bold text-xl text-[#2c2f31] mb-2">Siswa Tidak Ditemukan</h3>
                <p class="text-[#595c5e] mb-6 text-sm">Coba kata kunci yang berbeda atau tambah siswa baru.</p>
                <button wire:click="openCreateForm"
                    class="px-6 py-3 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.02] transition-transform shadow-sm inline-flex items-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-sm">person_add</span> Tambah Siswa
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[400px]">
                    <thead>
                        <tr class="bg-[#f5f7f9]">
                            <th class="text-left px-4 md:px-6 py-3 text-xs font-bold uppercase tracking-wider text-[#595c5e]">Siswa</th>
                            <th class="text-left px-4 md:px-6 py-3 text-xs font-bold uppercase tracking-wider text-[#595c5e] hidden md:table-cell">NIS/NISN</th>
                            <th class="text-left px-4 md:px-6 py-3 text-xs font-bold uppercase tracking-wider text-[#595c5e] hidden lg:table-cell">Kelas</th>
                            <th class="text-left px-4 md:px-6 py-3 text-xs font-bold uppercase tracking-wider text-[#595c5e] hidden sm:table-cell">Kursus</th>
                            <th class="text-left px-4 md:px-6 py-3 text-xs font-bold uppercase tracking-wider text-[#595c5e] hidden lg:table-cell">XP</th>
                            <th class="text-right px-4 md:px-6 py-3 text-xs font-bold uppercase tracking-wider text-[#595c5e]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f5f7f9]">
                        @foreach ($students as $student)
                            <tr class="hover:bg-[#f5f7f9] transition-colors group">
                                <td class="px-4 md:px-6 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="relative flex-shrink-0">
                                            <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}"
                                                class="w-9 h-9 rounded-xl object-cover">
                                            @if ($student->streak_days >= 7)
                                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-orange-500 rounded-full border-2 border-white flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-white" style="font-size: 8px; font-variation-settings: 'FILL' 1;">local_fire_department</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-[#2c2f31] text-sm truncate group-hover:text-[#0058ba] transition-colors">{{ $student->name }}</p>
                                            <p class="text-xs text-[#595c5e] truncate">{{ $student->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 py-3 hidden md:table-cell">
                                    <div class="space-y-0.5">
                                        <p class="text-xs text-[#595c5e]">NIS: <span class="font-mono font-semibold text-[#2c2f31]">{{ $student->nis ?? '-' }}</span></p>
                                        <p class="text-xs text-[#595c5e]">NISN: <span class="font-mono font-semibold text-[#2c2f31]">{{ $student->nisn ?? '-' }}</span></p>
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 py-3 hidden lg:table-cell">
                                    @if ($student->class_group)
                                        <span class="px-2.5 py-1 bg-[#0058ba]/10 text-[#0058ba] rounded-lg text-xs font-bold">{{ $student->class_group }}</span>
                                    @else
                                        <span class="text-xs text-[#595c5e]">-</span>
                                    @endif
                                </td>
                                <td class="px-4 md:px-6 py-3 hidden sm:table-cell">
                                    <div class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[#595c5e]" style="font-size: 14px;">school</span>
                                        <span class="text-sm font-semibold text-[#2c2f31]">{{ $student->enrollments_count }}</span>
                                        @if ($student->completed_count > 0)
                                            <span class="text-xs text-[#00675c] hidden md:inline">({{ $student->completed_count }}✓)</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 py-3 hidden lg:table-cell">
                                    <div class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-amber-500" style="font-size: 14px; font-variation-settings: 'FILL' 1;">star</span>
                                        <span class="text-sm font-bold text-[#2c2f31]">{{ number_format($student->xp_points) }}</span>
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <button wire:click="viewDetail({{ $student->id }})"
                                            class="p-1.5 text-[#595c5e] hover:text-[#0058ba] hover:bg-blue-50 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-sm">visibility</span>
                                        </button>
                                        @if (!$isInstructor)
                                            <button wire:click="openEditForm({{ $student->id }})"
                                                class="p-1.5 text-[#595c5e] hover:text-[#0058ba] hover:bg-blue-50 rounded-lg transition-colors">
                                                <span class="material-symbols-outlined text-sm">edit</span>
                                            </button>
                                            <button wire:click="confirmDelete({{ $student->id }})"
                                                class="p-1.5 text-[#595c5e] hover:text-[#b31b25] hover:bg-red-50 rounded-lg transition-colors">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 md:px-6 py-4 border-t border-[#f5f7f9]">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════════════════════
         CREATE / EDIT MODAL
         ════════════════════════════════════════════════════════ --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
            wire:click.self="$set('showForm', false)">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div
                    class="p-6 border-b border-[#eef1f3] flex items-center justify-between sticky top-0 bg-white z-10">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-[#0058ba] to-[#6c9fff] rounded-xl flex items-center justify-center">
                            <span
                                class="material-symbols-outlined text-white text-sm">{{ $editingStudentId ? 'edit' : 'person_add' }}</span>
                        </div>
                        <h3 class="font-headline font-bold text-xl text-[#2c2f31]">
                            {{ $editingStudentId ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}
                        </h3>
                    </div>
                    <button wire:click="$set('showForm', false)"
                        class="p-2 rounded-full hover:bg-[#eef1f3] transition-colors">
                        <span class="material-symbols-outlined text-[#595c5e]">close</span>
                    </button>
                </div>

                <form wire:submit="save" class="p-6 space-y-5">
                    {{-- Name & Email --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Nama Lengkap *</label>
                            <input type="text" wire:model="name" placeholder="Nama lengkap siswa..."
                                class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Email *</label>
                            <input type="email" wire:model="email" placeholder="email@example.com"
                                class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Password & NIS --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1">
                                Password {{ $editingStudentId ? '(kosongkan jika tidak diubah)' : '*' }}
                            </label>
                            <input type="password" wire:model="password" placeholder="Minimal 6 karakter"
                                class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1">NIS</label>
                            <input type="text" wire:model="nis" placeholder="Nomor Induk Siswa"
                                class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                            @error('nis')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- NISN --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">NISN <span
                                class="text-[#595c5e] font-normal text-xs">(Nomor Induk Siswa Nasional)</span></label>
                        <input type="text" wire:model="nisn" placeholder="10 digit NISN"
                            class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20"
                            maxlength="10">
                        @error('nisn')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Gender & Birth Date --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Jenis Kelamin</label>
                            <select wire:model="gender"
                                class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                                <option value="">Pilih...</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            @error('gender')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Tanggal Lahir</label>
                            <input type="date" wire:model="birth_date"
                                class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                            @error('birth_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Class & Phone --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Kelas / Rombel</label>
                            <input type="text" wire:model="class_group" placeholder="Contoh: VII-A, X IPA 1"
                                class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                            @error('class_group')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1">No. Telepon</label>
                            <input type="text" wire:model="phone" placeholder="08xxxxxxxxxx"
                                class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Guardian Name --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Nama Wali / Orang Tua</label>
                        <input type="text" wire:model="guardian_name" placeholder="Nama wali murid..."
                            class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                        @error('guardian_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Alamat</label>
                        <textarea wire:model="address" rows="2" placeholder="Alamat lengkap siswa..."
                            class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 resize-none"></textarea>
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-3 pt-2">
                        <button type="button" wire:click="$set('showForm', false)"
                            class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.01] transition-transform shadow-sm">
                            <span wire:loading wire:target="save" class="inline-block animate-spin mr-1">⟳</span>
                            {{ $editingStudentId ? 'Perbarui Data' : 'Tambah Siswa' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════
         DETAIL MODAL
         ════════════════════════════════════════════════════════ --}}
    @if ($showDetailModal && $viewingStudent)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
            wire:click.self="$set('showDetailModal', false)">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                {{-- Header with gradient --}}
                <div class="bg-gradient-to-br from-[#0058ba] to-[#004da4] p-6 rounded-t-xl relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white rounded-full"></div>
                        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-white rounded-full"></div>
                    </div>
                    <div class="relative flex items-center gap-4">
                        <img src="{{ $viewingStudent->avatar_url }}" alt="{{ $viewingStudent->name }}"
                            class="w-16 h-16 rounded-2xl object-cover border-2 border-white/30">
                        <div>
                            <h3 class="font-headline font-bold text-xl text-white">{{ $viewingStudent->name }}</h3>
                            <p class="text-white/70 text-sm">{{ $viewingStudent->email }}</p>
                            @if ($viewingStudent->nis)
                                <p class="text-white/50 text-xs font-mono mt-0.5">NIS: {{ $viewingStudent->nis }}</p>
                            @endif
                            @if ($viewingStudent->nisn)
                                <p class="text-white/50 text-xs font-mono mt-0.5">NISN: {{ $viewingStudent->nisn }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <button wire:click="$set('showDetailModal', false)"
                        class="absolute top-4 right-4 p-1.5 rounded-full bg-white/10 hover:bg-white/20 transition-colors">
                        <span class="material-symbols-outlined text-white text-sm">close</span>
                    </button>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-3 p-6 -mt-4">
                    <div class="bg-[#eef1f3] p-3 rounded-xl text-center">
                        <p class="text-lg font-headline font-bold text-[#0058ba]">
                            {{ $viewingStudent->enrollments_count }}</p>
                        <p class="text-xs text-[#595c5e]">Kursus</p>
                    </div>
                    <div class="bg-[#eef1f3] p-3 rounded-xl text-center">
                        <p class="text-lg font-headline font-bold text-[#00675c]">
                            {{ $viewingStudent->completed_count }}</p>
                        <p class="text-xs text-[#595c5e]">Selesai</p>
                    </div>
                    <div class="bg-[#eef1f3] p-3 rounded-xl text-center">
                        <p class="text-lg font-headline font-bold text-amber-600">
                            {{ number_format($viewingStudent->xp_points) }}</p>
                        <p class="text-xs text-[#595c5e]">XP Points</p>
                    </div>
                </div>

                {{-- Info List --}}
                <div class="px-6 pb-6 space-y-3">
                    <div class="flex items-center gap-3 py-2.5 border-b border-[#f5f7f9]">
                        <span class="material-symbols-outlined text-[#595c5e] text-sm">tag</span>
                        <span class="text-sm text-[#595c5e] w-28 shrink-0">NISN</span>
                        <span
                            class="text-sm font-semibold text-[#2c2f31] font-mono">{{ $viewingStudent->nisn ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-3 py-2.5 border-b border-[#f5f7f9]">
                        <span class="material-symbols-outlined text-[#595c5e] text-sm">badge</span>
                        <span class="text-sm text-[#595c5e] w-28 shrink-0">Jenis Kelamin</span>
                        <span class="text-sm font-semibold text-[#2c2f31]">
                            {{ $viewingStudent->gender === 'L' ? 'Laki-laki' : ($viewingStudent->gender === 'P' ? 'Perempuan' : '-') }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 py-2.5 border-b border-[#f5f7f9]">
                        <span class="material-symbols-outlined text-[#595c5e] text-sm">cake</span>
                        <span class="text-sm text-[#595c5e] w-28 shrink-0">Tanggal Lahir</span>
                        <span class="text-sm font-semibold text-[#2c2f31]">
                            {{ $viewingStudent->birth_date ? $viewingStudent->birth_date->format('d F Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 py-2.5 border-b border-[#f5f7f9]">
                        <span class="material-symbols-outlined text-[#595c5e] text-sm">school</span>
                        <span class="text-sm text-[#595c5e] w-28 shrink-0">Kelas</span>
                        <span
                            class="text-sm font-semibold text-[#2c2f31]">{{ $viewingStudent->class_group ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-3 py-2.5 border-b border-[#f5f7f9]">
                        <span class="material-symbols-outlined text-[#595c5e] text-sm">call</span>
                        <span class="text-sm text-[#595c5e] w-28 shrink-0">Telepon</span>
                        <span class="text-sm font-semibold text-[#2c2f31]">{{ $viewingStudent->phone ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-3 py-2.5 border-b border-[#f5f7f9]">
                        <span class="material-symbols-outlined text-[#595c5e] text-sm">family_restroom</span>
                        <span class="text-sm text-[#595c5e] w-28 shrink-0">Wali Murid</span>
                        <span
                            class="text-sm font-semibold text-[#2c2f31]">{{ $viewingStudent->guardian_name ?? '-' }}</span>
                    </div>
                    <div class="flex items-start gap-3 py-2.5 border-b border-[#f5f7f9]">
                        <span class="material-symbols-outlined text-[#595c5e] text-sm mt-0.5">location_on</span>
                        <span class="text-sm text-[#595c5e] w-28 shrink-0">Alamat</span>
                        <span
                            class="text-sm font-semibold text-[#2c2f31]">{{ $viewingStudent->address ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-3 py-2.5">
                        <span class="material-symbols-outlined text-[#595c5e] text-sm">calendar_today</span>
                        <span class="text-sm text-[#595c5e] w-28 shrink-0">Bergabung</span>
                        <span
                            class="text-sm font-semibold text-[#2c2f31]">{{ $viewingStudent->created_at->format('d F Y') }}</span>
                    </div>

                    @if ($viewingStudent->streak_days > 0)
                        <div class="mt-2 px-4 py-3 bg-orange-50 rounded-xl flex items-center gap-3">
                            <span class="material-symbols-outlined text-orange-500"
                                style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                            <span class="text-sm font-semibold text-orange-700">{{ $viewingStudent->streak_days }}
                                hari streak aktif!</span>
                        </div>
                    @endif

                    {{-- Action buttons --}}
                    <div class="flex gap-3 pt-4">
                        <button wire:click="$set('showDetailModal', false)"
                            class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors">
                            Tutup
                        </button>
                        <button wire:click="openEditForm({{ $viewingStudent->id }}); $set('showDetailModal', false)"
                            class="flex-1 py-3 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.01] transition-transform shadow-sm flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-sm">edit</span>
                            Edit Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════
         IMPORT MODAL
         ════════════════════════════════════════════════════════ --}}
    @if ($showImportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
            wire:click.self="$set('showImportModal', false)">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg">
                <div class="p-6 border-b border-[#eef1f3] flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-[#00675c] to-[#73f2dd] rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-sm">upload_file</span>
                        </div>
                        <h3 class="font-headline font-bold text-xl text-[#2c2f31]">Import Data Siswa</h3>
                    </div>
                    <button wire:click="$set('showImportModal', false)"
                        class="p-2 rounded-full hover:bg-[#eef1f3] transition-colors">
                        <span class="material-symbols-outlined text-[#595c5e]">close</span>
                    </button>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Instructions --}}
                    <div class="bg-[#0058ba]/5 border border-[#0058ba]/10 rounded-xl p-4 space-y-2">
                        <p class="text-sm font-semibold text-[#0058ba] flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">info</span>
                            Petunjuk Import
                        </p>
                        <ul class="text-xs text-[#595c5e] space-y-1 ml-6 list-disc">
                            <li>Format file: <strong>.xlsx, .xls, atau .csv</strong></li>
                            <li>Kolom wajib: <strong>nama_lengkap</strong> dan <strong>email</strong></li>
                            <li>Kolom opsional: nis, jenis_kelamin, tanggal_lahir, no_telepon, alamat, kelas, nama_wali,
                                password</li>
                            <li>Password default: <strong>password123</strong> jika tidak diisi</li>
                            <li>Data duplikat (email/NIS sama) akan otomatis dilewati</li>
                        </ul>
                    </div>

                    {{-- Download Template --}}
                    <button wire:click="downloadTemplate"
                        class="w-full py-3 bg-[#eef1f3] text-[#2c2f31] font-bold rounded-xl hover:bg-[#dfe3e6] transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">description</span>
                        Download Template Excel
                    </button>

                    {{-- File Input --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-2">Pilih File Excel</label>
                        <div class="relative">
                            <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv"
                                class="w-full px-4 py-3 bg-[#eef1f3] border-2 border-dashed border-[#abadaf]/30 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#0058ba]/10 file:text-[#0058ba] hover:file:bg-[#0058ba]/20">
                        </div>
                        <div wire:loading wire:target="importFile"
                            class="flex items-center gap-2 text-[#595c5e] text-xs mt-2">
                            <div
                                class="w-3 h-3 border-2 border-[#0058ba] border-t-transparent rounded-full animate-spin">
                            </div>
                            Mengupload file...
                        </div>
                        @error('importFile')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Import Result --}}
                    @if ($importResult)
                        <div
                            class="px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2
                    {{ $importResultType === 'success' ? 'bg-[#73f2dd]/30 text-[#00675c]' : ($importResultType === 'warning' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-[#b31b25]') }}">
                            <span class="material-symbols-outlined text-sm">
                                {{ $importResultType === 'success' ? 'check_circle' : ($importResultType === 'warning' ? 'warning' : 'error') }}
                            </span>
                            {{ $importResult }}
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="flex gap-3 pt-2">
                        <button wire:click="$set('showImportModal', false)"
                            class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors">
                            {{ $importResult ? 'Selesai' : 'Batal' }}
                        </button>
                        @if (!$importResult || $importResultType !== 'success')
                            <button wire:click="importExcel" {{ !$importFile ? 'disabled' : '' }}
                                class="flex-1 py-3 bg-gradient-to-br from-[#00675c] to-[#004d44] text-white font-bold rounded-full hover:scale-[1.01] transition-transform shadow-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <span wire:loading wire:target="importExcel"
                                    class="inline-block animate-spin">⟳</span>
                                <span class="material-symbols-outlined text-sm" wire:loading.remove
                                    wire:target="importExcel">upload</span>
                                Import Sekarang
                            </button>
                        @endif
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
                <h3 class="font-headline font-bold text-xl text-[#2c2f31] mb-2">Hapus Siswa?</h3>
                <p class="text-[#595c5e] text-sm mb-6">Tindakan ini tidak dapat dibatalkan. Semua data termasuk progres
                    belajar siswa akan dihapus permanen.</p>
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
