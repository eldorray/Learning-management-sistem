<div class="p-4 md:p-8 max-w-5xl mx-auto space-y-6 md:space-y-8">

    <!-- Breadcrumb + Header -->
    <section>
        <nav class="flex items-center gap-1.5 text-sm text-[#595c5e] mb-4 flex-wrap">
            <a href="{{ route('admin.courses') }}" class="hover:text-[#0058ba] transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">library_books</span>
                <span class="hidden sm:inline">Manajemen Kursus</span>
                <span class="sm:hidden">Kursus</span>
            </a>
            <span class="material-symbols-outlined text-xs">chevron_right</span>
            <span class="text-[#2c2f31] font-semibold truncate max-w-[180px] sm:max-w-none">{{ $course->title }}</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs block mb-2">Course Builder</span>
                <h2 class="text-2xl md:text-3xl font-headline font-extrabold tracking-tight text-[#2c2f31]">{{ $course->title }}</h2>
                <p class="text-[#595c5e] mt-1 text-sm">Kelola modul dan pelajaran kursus ini.</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $course->is_published ? 'bg-[#73f2dd]/30 text-[#00675c]' : 'bg-[#eef1f3] text-[#595c5e]' }}">
                    {{ $course->is_published ? 'Dipublikasi' : 'Draft' }}
                </span>
                <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-[#0058ba]">
                    {{ $course->modules->count() }} Modul · {{ $course->modules->sum(fn($m) => $m->lessons->count()) }} Pelajaran
                </span>
            </div>
        </div>
    </section>

    <!-- Add Module Button -->
    <div class="flex justify-end">
        <button wire:click="openAddModule"
                class="px-5 py-2.5 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.02] transition-transform shadow-lg shadow-blue-500/20 flex items-center gap-2 text-sm">
            <span class="material-symbols-outlined text-sm">add</span>
            Tambah Modul
        </button>
    </div>

    <!-- Empty State -->
    @if($course->modules->isEmpty())
    <div class="bg-white rounded-2xl border border-[#abadaf]/10 shadow-sm p-12 md:p-16 text-center">
        <div class="w-20 h-20 bg-gradient-to-br from-[#0058ba]/10 to-[#6c9fff]/10 rounded-3xl flex items-center justify-center mx-auto mb-5">
            <span class="material-symbols-outlined text-[#0058ba] text-4xl">view_module</span>
        </div>
        <h3 class="font-headline font-bold text-xl text-[#2c2f31] mb-2">Belum ada modul</h3>
        <p class="text-[#595c5e] mb-6 max-w-md mx-auto text-sm">Buat modul pertama untuk mengorganisir pelajaran dalam kursus ini.</p>
        <button wire:click="openAddModule" class="btn-primary">
            <span class="material-symbols-outlined text-sm">add</span>
            Tambah Modul Pertama
        </button>
    </div>
    @else

    <!-- Modules List -->
    <div class="space-y-4">
        @foreach($course->modules as $module)
        <div class="bg-white rounded-2xl border border-[#abadaf]/10 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md"
             wire:key="module-{{ $module->id }}">

            <!-- Module Header -->
            <div class="flex items-center gap-3 px-4 md:px-6 py-4 cursor-pointer group"
                 wire:click="toggleModule({{ $module->id }})">

                <span class="material-symbols-outlined text-[#595c5e] transition-transform duration-300 {{ $expandedModuleId === $module->id ? 'rotate-90' : '' }} flex-shrink-0">
                    chevron_right
                </span>

                <div class="w-9 h-9 md:w-10 md:h-10 bg-gradient-to-br from-[#0058ba]/10 to-[#6c9fff]/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[#0058ba] text-sm md:text-base">folder_open</span>
                </div>

                <div class="flex-1 min-w-0">
                    <h3 class="font-headline font-bold text-[#2c2f31] group-hover:text-[#0058ba] transition-colors text-sm md:text-base truncate">
                        {{ $module->title }}
                    </h3>
                    <p class="text-xs text-[#595c5e]">{{ $module->lessons->count() }} pelajaran</p>
                </div>

                <!-- Module Actions -->
                <div class="flex items-center gap-0.5 md:gap-1" x-data @click.stop>
                    <button wire:click="moveModuleUp({{ $module->id }})"
                            class="p-1.5 text-[#595c5e] hover:text-[#0058ba] hover:bg-blue-50 rounded-lg transition-colors"
                            title="Pindah ke atas">
                        <span class="material-symbols-outlined text-sm">arrow_upward</span>
                    </button>
                    <button wire:click="moveModuleDown({{ $module->id }})"
                            class="p-1.5 text-[#595c5e] hover:text-[#0058ba] hover:bg-blue-50 rounded-lg transition-colors"
                            title="Pindah ke bawah">
                        <span class="material-symbols-outlined text-sm">arrow_downward</span>
                    </button>
                    <button wire:click="openEditModule({{ $module->id }})"
                            class="p-1.5 text-[#595c5e] hover:text-[#0058ba] hover:bg-blue-50 rounded-lg transition-colors"
                            title="Edit modul">
                        <span class="material-symbols-outlined text-sm">edit</span>
                    </button>
                    <button wire:click="confirmDeleteModule({{ $module->id }})"
                            class="p-1.5 text-[#595c5e] hover:text-[#b31b25] hover:bg-red-50 rounded-lg transition-colors"
                            title="Hapus modul">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </div>
            </div>

            <!-- Module Content (Expanded) -->
            @if($expandedModuleId === $module->id)
            <div class="border-t border-[#eef1f3]">
                @if($module->lessons->isEmpty())
                <div class="px-4 md:px-6 py-6 text-center">
                    <p class="text-[#595c5e] text-sm mb-3">Belum ada pelajaran di modul ini.</p>
                </div>
                @else
                <div class="divide-y divide-[#f5f7f9]">
                    @foreach($module->lessons as $lesson)
                    <div class="flex items-center gap-2.5 md:gap-3 px-4 md:px-6 py-3 hover:bg-[#f5f7f9]/50 transition-colors"
                         wire:key="lesson-{{ $lesson->id }}">

                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                            {{ $lesson->type === 'video' ? 'bg-purple-50 text-purple-600' : ($lesson->type === 'quiz' ? 'bg-amber-50 text-amber-600' : ($lesson->type === 'document' ? 'bg-emerald-50 text-emerald-600' : 'bg-[#eef1f3] text-[#595c5e]')) }}">
                            <span class="material-symbols-outlined text-sm">{{ $lesson->type_icon }}</span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-[#2c2f31] truncate">{{ $lesson->title }}</p>
                            <div class="flex items-center gap-1.5 text-xs text-[#595c5e]">
                                <span>{{ match($lesson->type) { 'document' => 'Dokumen', 'video' => 'Video', 'quiz' => 'Kuis', default => 'Teks' } }}</span>
                                @if($lesson->duration_minutes > 0)
                                <span>·</span>
                                <span>{{ $lesson->duration_minutes }}m</span>
                                @endif
                                @if($lesson->document_name)
                                <span>·</span>
                                <span class="flex items-center gap-0.5"><span class="material-symbols-outlined" style="font-size:11px">attach_file</span>{{ Str::limit($lesson->document_name, 15) }}</span>
                                @endif
                                @if($lesson->is_preview)
                                <span class="px-1.5 py-0.5 bg-[#73f2dd]/30 text-[#00675c] rounded text-[10px] font-bold">Preview</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-0.5">
                            <button wire:click="moveLessonUp({{ $lesson->id }})"
                                    class="p-1 text-[#595c5e] hover:text-[#0058ba] hover:bg-blue-50 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-xs">arrow_upward</span>
                            </button>
                            <button wire:click="moveLessonDown({{ $lesson->id }})"
                                    class="p-1 text-[#595c5e] hover:text-[#0058ba] hover:bg-blue-50 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-xs">arrow_downward</span>
                            </button>
                            <a href="{{ route('admin.lesson.edit', $lesson->id) }}"
                               class="p-1 text-[#595c5e] hover:text-[#0058ba] hover:bg-blue-50 rounded-lg transition-colors"
                               title="Edit pelajaran">
                                <span class="material-symbols-outlined text-xs">edit</span>
                            </a>
                            <button wire:click="confirmDeleteLesson({{ $lesson->id }})"
                                    class="p-1 text-[#595c5e] hover:text-[#b31b25] hover:bg-red-50 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-xs">delete</span>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="px-4 md:px-6 py-3 bg-[#f5f7f9]/50">
                    <button wire:click="openAddLesson({{ $module->id }})"
                            class="w-full py-2.5 border-2 border-dashed border-[#abadaf]/30 rounded-xl text-sm font-semibold text-[#595c5e] hover:text-[#0058ba] hover:border-[#0058ba]/30 hover:bg-blue-50/30 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Tambah Pelajaran
                    </button>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- Module Form Modal -->
    @if($showModuleForm)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl w-full sm:max-w-md">
            <div class="absolute top-2 left-1/2 -translate-x-1/2 w-10 h-1 bg-[#abadaf]/30 rounded-full sm:hidden"></div>
            <div class="p-5 border-b border-[#eef1f3] flex items-center justify-between mt-2 sm:mt-0">
                <h3 class="font-headline font-bold text-lg text-[#2c2f31]">
                    {{ $editingModuleId ? 'Edit Modul' : 'Tambah Modul Baru' }}
                </h3>
                <button wire:click="$set('showModuleForm', false)" class="p-2 rounded-full hover:bg-[#eef1f3] transition-colors">
                    <span class="material-symbols-outlined text-[#595c5e]">close</span>
                </button>
            </div>
            <form wire:submit="saveModule" class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Nama Modul *</label>
                    <input type="text" wire:model="moduleName"
                           placeholder="Contoh: Pengantar Bahasa Arab"
                           autofocus
                           class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm">
                    @error('moduleName')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showModuleForm', false)"
                            class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors text-sm">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-3 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.01] transition-transform shadow-sm text-sm">
                        <span wire:loading wire:target="saveModule" class="inline-block animate-spin mr-1">⟳</span>
                        {{ $editingModuleId ? 'Perbarui' : 'Buat Modul' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Lesson Form Modal -->
    @if($showLessonForm)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl w-full sm:max-w-lg max-h-[95vh] sm:max-h-[90vh] overflow-y-auto">
            <div class="absolute top-2 left-1/2 -translate-x-1/2 w-10 h-1 bg-[#abadaf]/30 rounded-full sm:hidden"></div>
            <div class="p-5 border-b border-[#eef1f3] flex items-center justify-between sticky top-0 bg-white z-10 mt-2 sm:mt-0">
                <h3 class="font-headline font-bold text-lg text-[#2c2f31]">
                    {{ $editingLessonId ? 'Edit Pelajaran' : 'Tambah Pelajaran Baru' }}
                </h3>
                <button wire:click="$set('showLessonForm', false)" class="p-2 rounded-full hover:bg-[#eef1f3] transition-colors">
                    <span class="material-symbols-outlined text-[#595c5e]">close</span>
                </button>
            </div>
            <form wire:submit="saveLesson" class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Judul Pelajaran *</label>
                    <input type="text" wire:model="lessonTitle"
                           placeholder="Contoh: Huruf Hijaiyah"
                           autofocus
                           class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm">
                    @error('lessonTitle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-2">Tipe Pelajaran</label>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach(['text' => ['article', 'Teks', 'text-[#595c5e]'], 'video' => ['play_circle', 'Video', 'text-purple-500'], 'document' => ['description', 'Dokumen', 'text-emerald-500'], 'quiz' => ['quiz', 'Kuis', 'text-amber-500']] as $type => [$icon, $label, $color])
                        <label class="relative cursor-pointer">
                            <input type="radio" wire:model.live="lessonType" value="{{ $type }}" class="peer sr-only">
                            <div class="p-3 rounded-xl border-2 text-center transition-all
                                        peer-checked:border-[#0058ba] peer-checked:bg-blue-50/50
                                        border-[#eef1f3] hover:border-[#abadaf]/40">
                                <span class="material-symbols-outlined text-lg block mb-1 {{ $color }}">
                                    {{ $icon }}
                                </span>
                                <span class="text-xs font-semibold text-[#2c2f31]">{{ $label }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Durasi (menit)</label>
                        <input type="number" wire:model="lessonDuration" min="0"
                               class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm">
                    </div>
                    <div class="flex items-end pb-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="lessonIsPreview" class="rounded">
                            <span class="text-sm font-medium text-[#2c2f31]">Preview gratis</span>
                        </label>
                    </div>
                </div>

                @if($lessonType === 'video')
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1">URL Video</label>
                    <input type="url" wire:model="lessonVideoUrl"
                           placeholder="https://youtube.com/watch?v=..."
                           class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm">
                    @error('lessonVideoUrl')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                @endif

                @if($lessonType === 'text')
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Konten</label>
                    <textarea wire:model="lessonContent" rows="4"
                              placeholder="Tulis konten pelajaran di sini..."
                              class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 resize-none text-sm"></textarea>
                </div>
                @endif

                {{-- Document Upload (available for all types) --}}
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Lampiran Dokumen <span class="text-[#595c5e] font-normal">(opsional)</span></label>

                    @if($existingDocumentName && !$lessonDocument)
                    <div class="flex items-center gap-3 p-3 bg-[#eef1f3] rounded-xl">
                        <div class="w-9 h-9 bg-emerald-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-emerald-600 text-sm">description</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-[#2c2f31] truncate">{{ $existingDocumentName }}</p>
                            <p class="text-xs text-[#595c5e]">Dokumen saat ini</p>
                        </div>
                        <button type="button" wire:click="removeDocument" wire:confirm="Hapus dokumen ini?"
                                class="p-1.5 text-[#b31b25] hover:bg-red-50 rounded-lg transition-colors flex-shrink-0">
                            <span class="material-symbols-outlined text-sm">delete</span>
                        </button>
                    </div>
                    @else
                    <div class="relative">
                        <input type="file" wire:model="lessonDocument" id="lessonDocumentInput"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="border-2 border-dashed border-[#abadaf]/30 rounded-xl p-5 text-center hover:border-[#0058ba]/30 hover:bg-blue-50/20 transition-all">
                            <span class="material-symbols-outlined text-2xl text-[#595c5e] block mb-2">cloud_upload</span>
                            <p class="text-sm font-semibold text-[#2c2f31]">
                                @if($lessonDocument)
                                    <span class="text-[#00675c]">{{ $lessonDocument->getClientOriginalName() }}</span>
                                @else
                                    Klik atau seret file ke sini
                                @endif
                            </p>
                            <p class="text-xs text-[#595c5e] mt-1">PDF, Word, Excel, PPT, TXT, ZIP · Max 20MB</p>
                        </div>
                    </div>
                    @endif
                    @error('lessonDocument')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <div wire:loading wire:target="lessonDocument" class="mt-2 flex items-center gap-2 text-xs text-[#0058ba]">
                        <span class="inline-block animate-spin">⟳</span> Mengunggah dokumen...
                    </div>
                </div>

                <div class="bg-[#eef1f3] rounded-xl p-3 text-xs text-[#595c5e] flex items-start gap-2">
                    <span class="material-symbols-outlined text-sm mt-0.5 flex-shrink-0">info</span>
                    <span>
                        @if($lessonType === 'quiz')
                            Setelah menyimpan, Anda akan diarahkan ke editor kuis.
                        @else
                            Anda bisa mengedit konten lebih lengkap melalui halaman editor setelah menyimpan.
                        @endif
                    </span>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showLessonForm', false)"
                            class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors text-sm">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-3 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.01] transition-transform shadow-sm text-sm">
                        <span wire:loading wire:target="saveLesson" class="inline-block animate-spin mr-1">⟳</span>
                        {{ $editingLessonId ? 'Perbarui' : 'Buat Pelajaran' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Delete Module Confirmation -->
    @if($showDeleteModuleModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 text-center">
            <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[#b31b25] text-3xl">folder_delete</span>
            </div>
            <h3 class="font-headline font-bold text-xl text-[#2c2f31] mb-2">Hapus Modul?</h3>
            <p class="text-[#595c5e] text-sm mb-6">Semua pelajaran dalam modul ini juga akan dihapus permanen.</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteModuleModal', false)"
                        class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full text-sm">Batal</button>
                <button wire:click="deleteModule"
                        class="flex-1 py-3 bg-[#b31b25] text-white font-bold rounded-full hover:bg-[#9f0519] transition-colors text-sm">Hapus</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Lesson Confirmation -->
    @if($showDeleteLessonModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 text-center">
            <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[#b31b25] text-3xl">delete_forever</span>
            </div>
            <h3 class="font-headline font-bold text-xl text-[#2c2f31] mb-2">Hapus Pelajaran?</h3>
            <p class="text-[#595c5e] text-sm mb-6">Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteLessonModal', false)"
                        class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full text-sm">Batal</button>
                <button wire:click="deleteLesson"
                        class="flex-1 py-3 bg-[#b31b25] text-white font-bold rounded-full hover:bg-[#9f0519] transition-colors text-sm">Hapus</button>
            </div>
        </div>
    </div>
    @endif

</div>
