<div class="p-4 md:p-8 max-w-7xl mx-auto space-y-6 md:space-y-8">

    <!-- Header -->
    <section class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs block mb-2">Admin Panel</span>
            <h2 class="text-2xl md:text-4xl font-headline font-extrabold tracking-tight text-[#2c2f31]">Manajemen Kursus</h2>
            <p class="text-[#595c5e] mt-1 text-sm">Kelola seluruh kursus dalam platform.</p>
        </div>
        <button wire:click="openCreateForm"
                class="self-start sm:self-auto px-5 py-2.5 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.02] transition-transform shadow-lg shadow-blue-500/20 flex items-center gap-2 text-sm">
            <span class="material-symbols-outlined text-sm">add</span>
            Tambah Kursus
        </button>
    </section>

    <!-- Flash Message -->
    @if(session('success'))
    <div class="bg-[#73f2dd]/30 border border-[#00675c]/20 text-[#00675c] px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
        <span class="material-symbols-outlined text-sm">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <div class="flex flex-col gap-3">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#595c5e] text-sm">search</span>
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="Cari kursus..."
                   class="w-full pl-12 pr-4 py-3 bg-white border border-[#abadaf]/20 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
        </div>
        <div class="flex gap-3 flex-wrap">
            <select wire:model.live="filterLevel"
                    class="flex-1 min-w-[120px] px-4 py-2.5 bg-white border border-[#abadaf]/20 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                <option value="">Semua Level</option>
                <option value="beginner">Pemula</option>
                <option value="intermediate">Menengah</option>
                <option value="advanced">Mahir</option>
            </select>
            <select wire:model.live="filterStatus"
                    class="flex-1 min-w-[120px] px-4 py-2.5 bg-white border border-[#abadaf]/20 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                <option value="">Semua Status</option>
                <option value="published">Dipublikasi</option>
                <option value="draft">Draft</option>
            </select>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
        @if($courses->isEmpty())
        <div class="p-12 md:p-16 text-center">
            <div class="w-16 h-16 bg-[#eef1f3] rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[#595c5e] text-3xl">library_books</span>
            </div>
            <h3 class="font-headline font-bold text-xl text-[#2c2f31] mb-2">Belum ada kursus</h3>
            <p class="text-[#595c5e] mb-6 text-sm">Buat kursus pertama Anda sekarang.</p>
            <button wire:click="openCreateForm" class="btn-primary">
                <span class="material-symbols-outlined text-sm">add</span> Buat Kursus
            </button>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[500px]">
                <thead>
                    <tr class="bg-[#f5f7f9]">
                        <th class="text-left px-4 md:px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#595c5e]">Kursus</th>
                        <th class="text-left px-4 md:px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#595c5e] hidden md:table-cell">Kode</th>
                        <th class="text-left px-4 md:px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#595c5e] hidden sm:table-cell">Level</th>
                        <th class="text-left px-4 md:px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#595c5e]">Siswa</th>
                        <th class="text-left px-4 md:px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#595c5e]">Status</th>
                        <th class="text-right px-4 md:px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#595c5e]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f5f7f9]">
                    @foreach($courses as $course)
                    <tr class="hover:bg-[#f5f7f9] transition-colors">
                        <td class="px-4 md:px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-[#0058ba] to-[#6c9fff] rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-white text-sm">school</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-[#2c2f31] text-sm truncate max-w-[150px] md:max-w-none">{{ $course->title }}</p>
                                    <p class="text-xs text-[#595c5e]">{{ $course->total_lessons }} pelajaran</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 md:px-6 py-4 hidden md:table-cell">
                            <div class="flex items-center gap-1.5">
                                <code class="px-2.5 py-1 bg-[#0058ba]/10 text-[#0058ba] font-mono font-bold text-sm rounded-lg tracking-wider">{{ $course->enrollment_code }}</code>
                                <button wire:click="regenerateCode({{ $course->id }})"
                                        class="p-1 text-[#595c5e] hover:text-[#0058ba] hover:bg-blue-50 rounded transition-colors"
                                        title="Generate Kode Baru">
                                    <span class="material-symbols-outlined" style="font-size: 14px;">refresh</span>
                                </button>
                            </div>
                        </td>
                        <td class="px-4 md:px-6 py-4 hidden sm:table-cell">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                {{ $course->level === 'beginner' ? 'bg-green-50 text-green-700' : ($course->level === 'intermediate' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                {{ $course->level_badge }}
                            </span>
                        </td>
                        <td class="px-4 md:px-6 py-4">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[#595c5e] text-sm">group</span>
                                <span class="text-sm font-semibold text-[#2c2f31]">{{ $course->enrollments_count }}</span>
                            </div>
                        </td>
                        <td class="px-4 md:px-6 py-4">
                            <button wire:click="togglePublish({{ $course->id }})"
                                    class="px-2.5 py-1 rounded-full text-xs font-bold transition-colors
                                    {{ $course->is_published ? 'bg-[#73f2dd]/30 text-[#00675c] hover:bg-red-50 hover:text-red-600' : 'bg-[#eef1f3] text-[#595c5e] hover:bg-[#73f2dd]/30 hover:text-[#00675c]' }}">
                                {{ $course->is_published ? 'Aktif' : 'Draft' }}
                            </button>
                        </td>
                        <td class="px-4 md:px-6 py-4">
                            <div class="flex items-center justify-end gap-1 md:gap-2">
                                <a href="{{ route('admin.courses.builder', $course->id) }}"
                                   class="p-1.5 md:px-3 md:py-1.5 text-xs font-bold text-[#0058ba] bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors flex items-center gap-1"
                                   title="Builder">
                                    <span class="material-symbols-outlined text-xs">construction</span>
                                    <span class="hidden md:inline">Builder</span>
                                </a>
                                <a href="{{ route('admin.courses.students', $course->id) }}"
                                   class="p-1.5 md:px-3 md:py-1.5 text-xs font-bold text-[#00675c] bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors flex items-center gap-1"
                                   title="Siswa">
                                    <span class="material-symbols-outlined text-xs">group</span>
                                    <span class="hidden md:inline">Siswa</span>
                                </a>
                                <button wire:click="openEditForm({{ $course->id }})"
                                        class="p-1.5 text-[#595c5e] hover:text-[#0058ba] hover:bg-blue-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </button>
                                <button wire:click="confirmDelete({{ $course->id }})"
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
            {{ $courses->links() }}
        </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showForm)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4">
        <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-2xl w-full sm:max-w-2xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto">
            <div class="p-5 border-b border-[#eef1f3] flex items-center justify-between sticky top-0 bg-white z-10">
                <!-- Drag handle (mobile) -->
                <div class="absolute top-2 left-1/2 -translate-x-1/2 w-10 h-1 bg-[#abadaf]/30 rounded-full sm:hidden"></div>
                <h3 class="font-headline font-bold text-lg text-[#2c2f31]">
                    {{ $editingCourseId ? 'Edit Kursus' : 'Buat Kursus Baru' }}
                </h3>
                <button wire:click="$set('showForm', false)" class="p-2 rounded-full hover:bg-[#eef1f3]">
                    <span class="material-symbols-outlined text-[#595c5e]">close</span>
                </button>
            </div>

            <form wire:submit="save" class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Judul Kursus *</label>
                    <input type="text" wire:model="title" placeholder="Nama kursus yang menarik..."
                           class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Deskripsi Singkat</label>
                    <input type="text" wire:model="short_description" placeholder="Ringkasan singkat kursus..."
                           class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Deskripsi Lengkap</label>
                    <textarea wire:model="description" rows="3" placeholder="Jelaskan isi kursus secara detail..."
                              class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 resize-none text-sm"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Level</label>
                        <select wire:model="level"
                                class="w-full px-3 py-2.5 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm">
                            <option value="beginner">Pemula</option>
                            <option value="intermediate">Menengah</option>
                            <option value="advanced">Mahir</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Kategori</label>
                        <input type="text" wire:model="category" placeholder="Teknologi, Bisnis..."
                               class="w-full px-3 py-2.5 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Durasi (menit)</label>
                        <input type="number" wire:model="duration_minutes" min="0"
                               class="w-full px-3 py-2.5 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Thumbnail</label>
                        <input type="file" wire:model="thumbnail" accept="image/*"
                               class="w-full px-3 py-2 bg-[#eef1f3] border-none rounded-xl focus:outline-none text-xs">
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model.live="is_free" class="rounded">
                        <span class="text-sm font-medium text-[#2c2f31]">Kursus Gratis</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="is_published" class="rounded">
                        <span class="text-sm font-medium text-[#2c2f31]">Publikasikan</span>
                    </label>
                </div>

                @if(!$is_free)
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Harga (Rp)</label>
                    <input type="number" wire:model="price" min="0" step="1000"
                           class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm">
                </div>
                @endif

                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showForm', false)"
                            class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors text-sm">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-3 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.01] transition-transform shadow-sm text-sm">
                        <span wire:loading wire:target="save" class="inline-block animate-spin mr-1">⟳</span>
                        {{ $editingCourseId ? 'Perbarui Kursus' : 'Buat Kursus' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-8 mx-4 text-center">
            <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[#b31b25] text-3xl">delete_forever</span>
            </div>
            <h3 class="font-headline font-bold text-xl text-[#2c2f31] mb-2">Hapus Kursus?</h3>
            <p class="text-[#595c5e] text-sm mb-6">Tindakan ini tidak dapat dibatalkan. Semua data kursus akan dihapus permanen.</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteModal', false)"
                        class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full text-sm">Batal</button>
                <button wire:click="delete"
                        class="flex-1 py-3 bg-[#b31b25] text-white font-bold rounded-full hover:bg-[#9f0519] transition-colors text-sm">Hapus</button>
            </div>
        </div>
    </div>
    @endif

</div>
