<div class="p-4 md:p-8 max-w-5xl mx-auto space-y-6 md:space-y-8">

    <!-- Breadcrumb + Header -->
    <section>
        <nav class="flex items-center gap-1.5 text-sm text-[#595c5e] mb-4 flex-wrap">
            <a href="{{ route('admin.courses') }}" class="hover:text-[#0058ba] transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">library_books</span>
                <span class="hidden sm:inline">Kursus</span>
            </a>
            <span class="material-symbols-outlined text-xs">chevron_right</span>
            <a href="{{ route('admin.courses.builder', $lesson->course_id) }}" class="hover:text-[#0058ba] transition-colors truncate max-w-[100px] sm:max-w-none">
                {{ $lesson->course->title }}
            </a>
            <span class="material-symbols-outlined text-xs">chevron_right</span>
            <span class="text-[#2c2f31] font-semibold truncate max-w-[120px] sm:max-w-none">{{ $lesson->title }}</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center
                        {{ $lesson->type === 'video' ? 'bg-purple-50 text-purple-600' : ($lesson->type === 'quiz' ? 'bg-amber-50 text-amber-600' : 'bg-[#eef1f3] text-[#595c5e]') }}">
                        <span class="material-symbols-outlined text-sm">{{ $lesson->type_icon }}</span>
                    </span>
                    <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs">Lesson Editor</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-headline font-extrabold tracking-tight text-[#2c2f31]">{{ $lesson->title }}</h2>
                <p class="text-[#595c5e] mt-1 text-sm">
                    Modul: <span class="font-semibold">{{ $lesson->module->title }}</span>
                </p>
            </div>
            <a href="{{ route('admin.courses.builder', $lesson->course_id) }}"
               class="self-start sm:self-auto px-4 py-2.5 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors flex items-center gap-2 text-sm">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                <span class="hidden sm:inline">Kembali ke Builder</span>
                <span class="sm:hidden">Kembali</span>
            </a>
        </div>
    </section>

    <!-- Flash Message -->
    @if(session('success'))
    <div class="bg-[#73f2dd]/30 border border-[#00675c]/20 text-[#00675c] px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
        <span class="material-symbols-outlined text-sm">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- Lesson Info Card -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="bg-white rounded-2xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-[#eef1f3] flex items-center justify-between">
            <h3 class="font-headline font-bold text-lg text-[#2c2f31] flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0058ba]">tune</span>
                Informasi Pelajaran
            </h3>
        </div>
        <form wire:submit="saveLesson" class="p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Judul Pelajaran *</label>
                    <input type="text" wire:model="lessonTitle"
                           class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm">
                    @error('lessonTitle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-2">Tipe Pelajaran</label>
                    <div class="flex gap-3">
                        @foreach(['text' => ['article', 'Teks'], 'video' => ['play_circle', 'Video'], 'quiz' => ['quiz', 'Kuis']] as $type => [$icon, $label])
                        <label class="relative cursor-pointer flex-1">
                            <input type="radio" wire:model.live="lessonType" value="{{ $type }}" class="peer sr-only">
                            <div class="p-3 rounded-xl border-2 text-center transition-all
                                        peer-checked:border-[#0058ba] peer-checked:bg-blue-50/50
                                        border-[#eef1f3] hover:border-[#abadaf]/40">
                                <span class="material-symbols-outlined text-lg block mb-1
                                    {{ $type === 'text' ? 'text-[#595c5e]' : ($type === 'video' ? 'text-purple-500' : 'text-amber-500') }}">
                                    {{ $icon }}
                                </span>
                                <span class="text-xs font-semibold text-[#2c2f31]">{{ $label }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Durasi (menit)</label>
                        <input type="number" wire:model="lessonDuration" min="0"
                               class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm">
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="lessonIsPreview" class="rounded">
                        <span class="text-sm font-medium text-[#2c2f31]">Tampilkan sebagai preview gratis</span>
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

                @if($lesson->video_url)
                <div class="mt-3 p-3 bg-[#eef1f3] rounded-xl">
                    <p class="text-xs text-[#595c5e] mb-1">Video saat ini:</p>
                    <a href="{{ $lesson->video_url }}" target="_blank" class="text-sm text-[#0058ba] hover:underline flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">open_in_new</span>
                        {{ $lesson->video_url }}
                    </a>
                </div>
                @endif
            </div>
            @endif

            @if($lessonType === 'text')
            <div>
                <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Konten Materi</label>
                <textarea wire:model="lessonContent" rows="12"
                          placeholder="Tulis konten materi pelajaran di sini. Anda bisa menggunakan format teks biasa..."
                          class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 resize-y text-sm font-mono leading-relaxed"></textarea>
            </div>
            @endif

            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="px-8 py-3 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.02] transition-transform shadow-lg shadow-blue-500/20 flex items-center gap-2 text-sm">
                    <span wire:loading wire:target="saveLesson" class="inline-block animate-spin mr-1">⟳</span>
                    <span class="material-symbols-outlined text-sm">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- Quiz Section (only for quiz type) -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    @if($lessonType === 'quiz')
    <div class="bg-white rounded-2xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-[#eef1f3] flex items-center justify-between">
            <h3 class="font-headline font-bold text-lg text-[#2c2f31] flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500">quiz</span>
                Pertanyaan Kuis
                <span class="text-sm font-normal text-[#595c5e]">({{ $lesson->quizQuestions->count() }} soal)</span>
            </h3>
            <button wire:click="openAddQuestion"
                    class="px-4 py-2 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.02] transition-transform shadow-sm flex items-center gap-2 text-xs">
                <span class="material-symbols-outlined text-sm">add</span>
                Tambah Soal
            </button>
        </div>

        @if($lesson->quizQuestions->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-amber-500 text-3xl">help_outline</span>
            </div>
            <h4 class="font-headline font-bold text-lg text-[#2c2f31] mb-2">Belum ada pertanyaan</h4>
            <p class="text-[#595c5e] text-sm mb-4">Tambahkan soal untuk kuis ini.</p>
            <button wire:click="openAddQuestion" class="btn-primary text-sm">
                <span class="material-symbols-outlined text-sm">add</span>
                Tambah Pertanyaan Pertama
            </button>
        </div>
        @else
        <div class="divide-y divide-[#f5f7f9]">
            @foreach($lesson->quizQuestions as $qIndex => $question)
            <div class="p-6 hover:bg-[#f5f7f9]/50 transition-colors" wire:key="question-{{ $question->id }}">
                <div class="flex items-start gap-4">
                    <!-- Question Number -->
                    <div class="w-10 h-10 bg-gradient-to-br from-[#0058ba]/10 to-[#6c9fff]/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-[#0058ba]">{{ $qIndex + 1 }}</span>
                    </div>

                    <!-- Question Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div>
                                <p class="font-semibold text-[#2c2f31] text-sm">{{ $question->question }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                        {{ $question->type === 'multiple_choice' ? 'bg-blue-50 text-blue-600' : ($question->type === 'true_false' ? 'bg-green-50 text-green-600' : 'bg-purple-50 text-purple-600') }}">
                                        {{ $question->getTypeLabel() }}
                                    </span>
                                    <span class="text-[10px] text-[#595c5e]">{{ $question->points }} poin</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button wire:click="moveQuestionUp({{ $question->id }})"
                                        class="p-1 text-[#595c5e] hover:text-[#0058ba] hover:bg-blue-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-xs">arrow_upward</span>
                                </button>
                                <button wire:click="moveQuestionDown({{ $question->id }})"
                                        class="p-1 text-[#595c5e] hover:text-[#0058ba] hover:bg-blue-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-xs">arrow_downward</span>
                                </button>
                                <button wire:click="openEditQuestion({{ $question->id }})"
                                        class="p-1 text-[#595c5e] hover:text-[#0058ba] hover:bg-blue-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-xs">edit</span>
                                </button>
                                <button wire:click="confirmDeleteQuestion({{ $question->id }})"
                                        class="p-1 text-[#595c5e] hover:text-[#b31b25] hover:bg-red-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-xs">delete</span>
                                </button>
                            </div>
                        </div>

                        <!-- Options Preview -->
                        @if($question->type !== 'essay')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-3">
                            @foreach($question->options as $option)
                            <div class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs
                                {{ $option->is_correct ? 'bg-[#73f2dd]/20 text-[#00675c] border border-[#00675c]/20' : 'bg-[#eef1f3] text-[#595c5e]' }}">
                                <span class="material-symbols-outlined text-xs">
                                    {{ $option->is_correct ? 'check_circle' : 'radio_button_unchecked' }}
                                </span>
                                <span>{{ $option->option_text }}</span>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="mt-3 px-3 py-2 bg-[#eef1f3] rounded-lg text-xs text-[#595c5e] italic">
                            Jawaban esai (siswa akan mengetik jawaban)
                        </div>
                        @endif

                        <!-- Explanation -->
                        @if($question->explanation)
                        <div class="mt-3 px-3 py-2 bg-blue-50 rounded-lg text-xs text-[#0058ba] flex items-start gap-2">
                            <span class="material-symbols-outlined text-xs mt-0.5">lightbulb</span>
                            <span>{{ $question->explanation }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Total Points -->
        <div class="px-6 py-4 bg-[#f5f7f9] border-t border-[#eef1f3] flex items-center justify-between">
            <span class="text-sm text-[#595c5e]">Total Poin</span>
            <span class="font-headline font-bold text-[#0058ba]">{{ $lesson->quizQuestions->sum('points') }} poin</span>
        </div>
        @endif
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- Question Form Modal -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    @if($showQuestionForm)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-[#eef1f3] flex items-center justify-between sticky top-0 bg-white rounded-t-2xl z-10">
                <h3 class="font-headline font-bold text-xl text-[#2c2f31]">
                    {{ $editingQuestionId ? 'Edit Pertanyaan' : 'Tambah Pertanyaan Baru' }}
                </h3>
                <button wire:click="$set('showQuestionForm', false)" class="p-2 rounded-full hover:bg-[#eef1f3] transition-colors">
                    <span class="material-symbols-outlined text-[#595c5e]">close</span>
                </button>
            </div>

            <form wire:submit="saveQuestion" class="p-6 space-y-5">
                <!-- Question Text -->
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Pertanyaan *</label>
                    <textarea wire:model="questionText" rows="3"
                              placeholder="Tulis pertanyaan di sini..."
                              class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 resize-none text-sm"></textarea>
                    @error('questionText')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <!-- Question Type & Points -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-2">Tipe Soal</label>
                        <div class="space-y-2">
                            @foreach(['multiple_choice' => 'Pilihan Ganda', 'true_false' => 'Benar/Salah', 'essay' => 'Esai'] as $type => $label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model.live="questionType" value="{{ $type }}" class="text-[#0058ba]">
                                <span class="text-sm text-[#2c2f31]">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Poin</label>
                        <input type="number" wire:model="questionPoints" min="1" max="100"
                               class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm">
                    </div>
                </div>

                <!-- Options (for multiple_choice and true_false) -->
                @if($questionType !== 'essay')
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-2">
                        Opsi Jawaban
                        <span class="font-normal text-[#595c5e]">(klik radio untuk jawaban benar)</span>
                    </label>
                    @error('options')<p class="text-red-500 text-xs mb-2">{{ $message }}</p>@enderror

                    <div class="space-y-2">
                        @foreach($options as $i => $option)
                        <div class="flex items-center gap-2" wire:key="option-{{ $i }}">
                            <!-- Correct radio -->
                            <button type="button" wire:click="setCorrectOption({{ $i }})"
                                    class="w-6 h-6 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-colors
                                    {{ $option['is_correct'] ? 'border-[#00675c] bg-[#73f2dd]/30' : 'border-[#abadaf] hover:border-[#0058ba]' }}">
                                @if($option['is_correct'])
                                <span class="w-3 h-3 bg-[#00675c] rounded-full"></span>
                                @endif
                            </button>

                            <!-- Option text -->
                            <input type="text" wire:model="options.{{ $i }}.text"
                                   placeholder="Opsi {{ chr(65 + $i) }}"
                                   @if($questionType === 'true_false') readonly @endif
                                   class="flex-1 px-4 py-2.5 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-sm
                                          {{ $questionType === 'true_false' ? 'opacity-70 cursor-not-allowed' : '' }}">
                            @error("options.{$i}.text")<p class="text-red-500 text-xs">{{ $message }}</p>@enderror

                            <!-- Remove option (only for multiple choice with > 2 options) -->
                            @if($questionType === 'multiple_choice' && count($options) > 2)
                            <button type="button" wire:click="removeOption({{ $i }})"
                                    class="p-1 text-[#595c5e] hover:text-[#b31b25] hover:bg-red-50 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-sm">close</span>
                            </button>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    @if($questionType === 'multiple_choice' && count($options) < 6)
                    <button type="button" wire:click="addOption"
                            class="mt-2 text-sm text-[#0058ba] hover:text-[#004da4] font-semibold flex items-center gap-1 transition-colors">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Tambah Opsi
                    </button>
                    @endif
                </div>
                @else
                <div class="p-4 bg-[#eef1f3] rounded-xl text-sm text-[#595c5e] flex items-start gap-2">
                    <span class="material-symbols-outlined text-sm mt-0.5">info</span>
                    <span>Pertanyaan esai tidak memiliki opsi jawaban. Siswa akan mengetik jawaban secara bebas.</span>
                </div>
                @endif

                <!-- Explanation -->
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1">
                        Penjelasan Jawaban
                        <span class="font-normal text-[#595c5e]">(opsional)</span>
                    </label>
                    <textarea wire:model="questionExplanation" rows="2"
                              placeholder="Penjelasan mengapa jawaban tersebut benar..."
                              class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 resize-none text-sm"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showQuestionForm', false)"
                            class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors text-sm">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-3 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.01] transition-transform shadow-sm text-sm">
                        <span wire:loading wire:target="saveQuestion" class="inline-block animate-spin mr-1">⟳</span>
                        {{ $editingQuestionId ? 'Perbarui Soal' : 'Simpan Soal' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- Delete Question Confirmation -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    @if($showDeleteQuestionModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-8 mx-4 text-center">
            <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[#b31b25] text-3xl">delete_forever</span>
            </div>
            <h3 class="font-headline font-bold text-xl text-[#2c2f31] mb-2">Hapus Pertanyaan?</h3>
            <p class="text-[#595c5e] text-sm mb-6">Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteQuestionModal', false)"
                        class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full text-sm">Batal</button>
                <button wire:click="deleteQuestion"
                        class="flex-1 py-3 bg-[#b31b25] text-white font-bold rounded-full hover:bg-[#9f0519] transition-colors text-sm">Hapus</button>
            </div>
        </div>
    </div>
    @endif

</div>
