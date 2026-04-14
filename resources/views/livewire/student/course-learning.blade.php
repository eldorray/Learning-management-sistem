<div class="flex min-h-screen bg-[#f5f7f9]" x-data="{ mobileSidebar: false }">

    <!-- Mobile Sidebar Overlay -->
    <div x-show="mobileSidebar"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebar = false"
         class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden"
         style="display:none"></div>

    <!-- Left Content Area -->
    <div class="flex-1 min-w-0 flex flex-col">

        <!-- Course Top Bar -->
        <div class="bg-white/80 backdrop-blur-xl sticky top-0 z-10 border-b border-[#abadaf]/10 px-4 md:px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('student.courses') }}" class="p-2 rounded-full hover:bg-[#eef1f3] transition-colors flex-shrink-0">
                    <span class="material-symbols-outlined text-[#595c5e] text-xl">arrow_back</span>
                </a>
                <div class="min-w-0">
                    <p class="text-xs text-[#595c5e] uppercase tracking-wider font-semibold truncate">{{ $course->title }}</p>
                    @if($currentLesson)
                    <h2 class="font-headline font-bold text-[#2c2f31] text-sm md:text-base truncate">{{ $currentLesson->title }}</h2>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                <!-- Progress indicator (desktop) -->
                @if($enrollment)
                <div class="hidden md:flex items-center gap-3">
                    <div class="w-24 md:w-32 h-1.5 rounded-full bg-[#e5e9eb] overflow-hidden">
                        <div class="h-full bg-[#00675c] rounded-full transition-all duration-500"
                             style="width: {{ $enrollment->progress_percentage }}%"></div>
                    </div>
                    <span class="text-xs font-bold text-[#00675c]">{{ $enrollment->progress_percentage }}%</span>
                </div>
                @endif

                <!-- Toggle sidebar - Livewire (desktop) + Alpine (mobile) -->
                <button @click="mobileSidebar = !mobileSidebar"
                        class="lg:hidden p-2 rounded-full hover:bg-[#eef1f3] transition-colors">
                    <span class="material-symbols-outlined text-[#595c5e]">menu_book</span>
                </button>
                <button wire:click="$toggle('showSidebar')"
                        class="hidden lg:flex p-2 rounded-full hover:bg-[#eef1f3] transition-colors">
                    <span class="material-symbols-outlined text-[#595c5e]">
                        {{ $showSidebar ? 'menu_open' : 'menu' }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Lesson Content -->
        <div class="flex-1 p-4 md:p-8 lg:p-12 max-w-4xl">

            @if($currentLesson)

                <!-- Video Player -->
                @if($currentLesson->type === 'video' && $currentLesson->video_url)
                <div class="aspect-video bg-black rounded-xl overflow-hidden mb-6 md:mb-8 shadow-lg">
                    <iframe src="{{ $currentLesson->embed_url }}"
                            class="w-full h-full"
                            allow="autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            referrerpolicy="strict-origin-when-cross-origin"></iframe>
                </div>
                @endif

                <!-- Lesson Header -->
                <div class="mb-6 md:mb-8 space-y-3 md:space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 md:w-10 md:h-10 bg-[#eef1f3] rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-[#0058ba] text-lg">{{ $currentLesson->type_icon }}</span>
                        </div>
                        <span class="text-xs uppercase tracking-widest font-semibold text-[#595c5e]">
                            {{ match($currentLesson->type) { 'video' => 'Video', 'quiz' => 'Kuis', default => 'Artikel' } }}
                            @if($currentLesson->type === 'quiz' && $currentLesson->quizQuestions->count() > 0)
                                · {{ $currentLesson->quizQuestions->count() }} Soal
                            @elseif($currentLesson->duration_minutes > 0)
                                · {{ $currentLesson->duration_minutes }} menit
                            @endif
                        </span>
                    </div>
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-headline font-extrabold text-[#2c2f31] tracking-tight">
                        {{ $currentLesson->title }}
                    </h1>

                    @if($isCurrentCompleted)
                    <div class="flex items-center gap-2 text-[#00675c] text-sm font-semibold">
                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        Pelajaran ini sudah selesai
                    </div>
                    @endif
                </div>

                <!-- Text Content -->
                @if($currentLesson->content)
                <div class="prose prose-slate max-w-none text-[#2c2f31] text-base md:text-lg leading-relaxed mb-8 md:mb-12">
                    {!! nl2br(e($currentLesson->content)) !!}
                </div>
                @endif

                {{-- QUIZ SECTION --}}
                @if($currentLesson->type === 'quiz' && $currentLesson->quizQuestions->count() > 0)

                    @error('quiz')
                    <div class="bg-red-50 border border-red-200 text-[#b31b25] px-4 py-3 rounded-xl flex items-center gap-3 mb-6 text-sm">
                        <span class="material-symbols-outlined text-sm">error</span>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror

                    {{-- Quiz Result Banner --}}
                    @if($quizSubmitted)
                    <div class="mb-6 md:mb-8 rounded-xl overflow-hidden shadow-sm">
                        <div class="p-5 md:p-6 {{ $quizPassed ? 'bg-gradient-to-br from-[#00675c] to-[#004d44]' : 'bg-gradient-to-br from-[#b31b25] to-[#8a1019]' }} relative overflow-hidden">
                            <div class="absolute inset-0 opacity-10">
                                <div class="absolute -top-10 -right-10 w-40 h-40 bg-white rounded-full"></div>
                                <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-white rounded-full"></div>
                            </div>
                            <div class="relative flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 md:w-16 md:h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                                        <span class="material-symbols-outlined text-white text-2xl md:text-3xl" style="font-variation-settings: 'FILL' 1;">
                                            {{ $quizPassed ? 'emoji_events' : 'sentiment_dissatisfied' }}
                                        </span>
                                    </div>
                                    <div>
                                        <h3 class="font-headline font-bold text-lg md:text-xl text-white">
                                            {{ $quizPassed ? 'Selamat! Kuis Berhasil' : 'Belum Berhasil' }}
                                        </h3>
                                        <p class="text-white/80 text-xs md:text-sm">
                                            {{ $quizPassed ? 'Anda telah menyelesaikan kuis ini dengan baik.' : 'Nilai minimum kelulusan adalah 60%. Coba lagi!' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 md:gap-6">
                                    <div class="text-center">
                                        <p class="text-2xl md:text-3xl font-headline font-extrabold text-white">{{ $quizPercentage }}%</p>
                                        <p class="text-xs text-white/70 font-semibold">SKOR</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xl md:text-2xl font-headline font-bold text-white">{{ $quizCorrect }}/{{ $currentLesson->quizQuestions->where('type', '!=', 'essay')->count() }}</p>
                                        <p class="text-xs text-white/70 font-semibold">BENAR</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xl md:text-2xl font-headline font-bold text-white">{{ $quizScore }}</p>
                                        <p class="text-xs text-white/70 font-semibold">POIN</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(!$quizPassed)
                        <div class="bg-white p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border border-[#abadaf]/10">
                            <p class="text-sm text-[#595c5e]">Anda dapat mengulang kuis ini untuk mendapatkan nilai lebih baik.</p>
                            <button wire:click="retryQuiz"
                                    class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-white font-bold rounded-full text-sm hover:scale-[1.02] transition-transform shadow-sm flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-sm">replay</span>
                                Ulangi Kuis
                            </button>
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- Quiz Questions --}}
                    <div class="space-y-4 md:space-y-6 mb-8 md:mb-12">
                        @foreach($currentLesson->quizQuestions as $qIndex => $question)
                        <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden
                            {{ $quizSubmitted && isset($selectedAnswers[$question->id])
                                ? ($question->options->find($selectedAnswers[$question->id])?->is_correct ? 'ring-2 ring-[#00675c]/30' : 'ring-2 ring-[#b31b25]/30')
                                : '' }}">

                            <div class="px-4 md:px-6 py-3 md:py-4 bg-[#f5f7f9] border-b border-[#abadaf]/10 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-7 h-7 md:w-8 md:h-8 bg-[#0058ba] text-white text-xs md:text-sm font-bold rounded-full flex items-center justify-center">
                                        {{ $qIndex + 1 }}
                                    </span>
                                    <span class="text-xs font-bold text-[#595c5e] uppercase tracking-wider">
                                        {{ $question->getTypeLabel() }}
                                    </span>
                                </div>
                                <span class="text-xs font-semibold text-[#0058ba] bg-[#0058ba]/10 px-2.5 py-1 rounded-full">
                                    {{ $question->points }} poin
                                </span>
                            </div>

                            <div class="px-4 md:px-6 pt-4 pb-3">
                                <p class="text-[#2c2f31] font-semibold text-sm md:text-base leading-relaxed">
                                    {{ $question->question }}
                                </p>
                            </div>

                            @if($question->type !== 'essay')
                            <div class="px-4 md:px-6 pb-5 space-y-2">
                                @foreach($question->options as $option)
                                @php
                                    $isSelected = ($selectedAnswers[$question->id] ?? null) == $option->id;
                                    $isCorrectOption = $option->is_correct;
                                @endphp
                                <label class="flex items-center gap-3 p-3 md:p-4 rounded-xl cursor-pointer transition-all duration-200
                                    {{ $quizSubmitted
                                        ? ($isCorrectOption
                                            ? 'bg-[#73f2dd]/20 border-2 border-[#00675c]/30'
                                            : ($isSelected && !$isCorrectOption
                                                ? 'bg-red-50 border-2 border-[#b31b25]/30'
                                                : 'bg-[#f5f7f9] border-2 border-transparent'))
                                        : ($isSelected
                                            ? 'bg-[#0058ba]/10 border-2 border-[#0058ba]/30'
                                            : 'bg-[#f5f7f9] border-2 border-transparent hover:bg-[#eef1f3] hover:border-[#abadaf]/20')
                                    }}">
                                    <input type="radio"
                                           name="question_{{ $question->id }}"
                                           value="{{ $option->id }}"
                                           wire:model="selectedAnswers.{{ $question->id }}"
                                           {{ $quizSubmitted ? 'disabled' : '' }}
                                           class="w-4 h-4 md:w-5 md:h-5 text-[#0058ba] border-2 border-[#abadaf] focus:ring-[#0058ba]/20 flex-shrink-0 {{ $quizSubmitted ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                                    <span class="flex-1 text-sm font-medium {{ $quizSubmitted && $isCorrectOption ? 'text-[#00675c]' : ($quizSubmitted && $isSelected && !$isCorrectOption ? 'text-[#b31b25]' : 'text-[#2c2f31]') }}">
                                        {{ $option->option_text }}
                                    </span>

                                    @if($quizSubmitted)
                                        @if($isCorrectOption)
                                        <span class="material-symbols-outlined text-[#00675c] text-sm flex-shrink-0" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                        @elseif($isSelected && !$isCorrectOption)
                                        <span class="material-symbols-outlined text-[#b31b25] text-sm flex-shrink-0" style="font-variation-settings: 'FILL' 1;">cancel</span>
                                        @endif
                                    @endif
                                </label>
                                @endforeach
                            </div>
                            @endif

                            @if($question->type === 'essay')
                            <div class="px-4 md:px-6 pb-5">
                                <textarea wire:model="essayAnswers.{{ $question->id }}"
                                          rows="4"
                                          placeholder="Tulis jawaban Anda di sini..."
                                          {{ $quizSubmitted ? 'disabled' : '' }}
                                          class="w-full px-4 py-3 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 resize-none {{ $quizSubmitted ? 'cursor-not-allowed opacity-60' : '' }}"></textarea>
                                @if($quizSubmitted)
                                <p class="text-xs text-[#595c5e] mt-2 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">info</span>
                                    Jawaban esai akan dinilai oleh pengajar.
                                </p>
                                @endif
                            </div>
                            @endif

                            @if($quizSubmitted && $question->explanation)
                            <div class="mx-4 md:mx-6 mb-5 bg-[#0058ba]/5 border border-[#0058ba]/10 rounded-xl p-3 md:p-4">
                                <p class="text-sm font-semibold text-[#0058ba] flex items-center gap-2 mb-1">
                                    <span class="material-symbols-outlined text-sm">lightbulb</span>
                                    Penjelasan
                                </p>
                                <p class="text-sm text-[#595c5e] leading-relaxed">{{ $question->explanation }}</p>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    @if(!$quizSubmitted)
                    <div class="flex justify-center mb-8 md:mb-12">
                        <button wire:click="submitQuiz"
                                class="px-6 md:px-8 py-3 md:py-4 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full text-base md:text-lg hover:scale-[1.02] transition-transform shadow-lg shadow-blue-500/20 flex items-center gap-3">
                            <span wire:loading.remove wire:target="submitQuiz" class="material-symbols-outlined">send</span>
                            <span wire:loading wire:target="submitQuiz" class="inline-block animate-spin">⟳</span>
                            Kirim Jawaban
                        </button>
                    </div>
                    @endif

                @elseif($currentLesson->type === 'quiz' && $currentLesson->quizQuestions->count() === 0)
                    <div class="bg-white rounded-xl border border-[#abadaf]/10 p-10 md:p-12 text-center mb-8 md:mb-12">
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-[#eef1f3] rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-[#595c5e] text-2xl md:text-3xl">quiz</span>
                        </div>
                        <h3 class="font-headline font-bold text-lg md:text-xl text-[#2c2f31] mb-2">Kuis Belum Tersedia</h3>
                        <p class="text-[#595c5e] text-sm">Pengajar belum menambahkan soal. Silakan lanjut ke materi berikutnya.</p>
                    </div>
                @endif

                {{-- COURSE COMPLETION BANNER --}}
                @if($isCourseCompleted || $courseCompleted)
                <div class="bg-gradient-to-br from-[#00675c] to-[#005a50] rounded-2xl p-6 md:p-8 text-center mb-6 md:mb-8 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white rounded-full"></div>
                        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-white rounded-full"></div>
                    </div>
                    <div class="relative">
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-white text-2xl md:text-3xl" style="font-variation-settings: 'FILL' 1;">emoji_events</span>
                        </div>
                        <h3 class="font-headline font-extrabold text-xl md:text-2xl text-white mb-2">🎉 Kursus Selesai!</h3>
                        <p class="text-white/80 text-sm mb-4">Selamat! Anda telah menyelesaikan seluruh materi pada kursus ini.</p>
                        <a href="{{ route('student.dashboard') }}"
                           class="inline-flex items-center gap-2 px-5 md:px-6 py-3 bg-white text-[#00675c] font-bold rounded-full hover:bg-white/90 transition-colors shadow-lg text-sm">
                            <span class="material-symbols-outlined text-sm">arrow_back</span>
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 md:pt-8 border-t border-[#abadaf]/10 gap-2">
                    <button wire:click="previousLesson"
                            class="flex items-center gap-1.5 md:gap-2 px-4 md:px-6 py-2.5 md:py-3 bg-[#eef1f3] text-[#2c2f31] font-semibold rounded-full hover:bg-[#dfe3e6] transition-colors text-sm">
                        <span class="material-symbols-outlined text-lg">arrow_back</span>
                        <span class="hidden sm:inline">Sebelumnya</span>
                    </button>

                    <div class="flex items-center gap-2">
                        @if($currentLesson->type !== 'quiz')
                            @if(!$isCurrentCompleted)
                            <button wire:click="confirmMarkComplete"
                                    class="flex items-center gap-1.5 px-4 md:px-6 py-2.5 md:py-3 bg-[#00675c] text-white font-bold rounded-full hover:bg-[#005a50] transition-colors shadow-sm text-sm">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                <span class="hidden sm:inline">Tandai Selesai</span>
                                <span class="sm:hidden">Selesai</span>
                            </button>
                            @else
                            <div class="flex items-center gap-1.5 px-3 md:px-4 py-2.5 md:py-3 bg-[#73f2dd]/30 text-[#00675c] font-bold rounded-full text-sm">
                                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                <span class="hidden sm:inline">Selesai</span>
                            </div>
                            @endif
                        @else
                            @if($isCurrentCompleted)
                            <div class="flex items-center gap-1.5 px-3 md:px-4 py-2.5 md:py-3 bg-[#73f2dd]/30 text-[#00675c] font-bold rounded-full text-sm">
                                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                <span class="hidden sm:inline">Lulus</span>
                            </div>
                            @endif
                        @endif

                        @if($isLastLesson && $allLessonsCompleted && !$isCourseCompleted && !$courseCompleted)
                        <button wire:click="completeCourse"
                                class="flex items-center gap-1.5 px-4 md:px-8 py-2.5 md:py-3 bg-gradient-to-br from-[#00675c] to-[#005a50] text-white font-bold rounded-full hover:scale-[1.02] transition-transform shadow-lg shadow-emerald-500/20 animate-pulse text-sm">
                            <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">emoji_events</span>
                            <span class="hidden sm:inline">Selesaikan Kursus</span>
                            <span class="sm:hidden">Selesaikan</span>
                        </button>
                        @elseif(!$isLastLesson)
                        <button wire:click="nextLesson"
                                class="flex items-center gap-1.5 md:gap-2 px-4 md:px-6 py-2.5 md:py-3 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.02] transition-transform shadow-sm shadow-blue-500/20 text-sm">
                            <span class="hidden sm:inline">Selanjutnya</span>
                            <span class="material-symbols-outlined text-lg">arrow_forward</span>
                        </button>
                        @endif
                    </div>
                </div>

            @else
                <div class="text-center py-16 md:py-24">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-[#eef1f3] rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-[#595c5e] text-3xl md:text-4xl">school</span>
                    </div>
                    <h2 class="font-headline font-bold text-xl md:text-2xl text-[#2c2f31] mb-2">Pilih Pelajaran</h2>
                    <p class="text-[#595c5e] text-sm">Pilih pelajaran dari daftar materi untuk mulai belajar.</p>
                    <button @click="mobileSidebar = true"
                            class="mt-4 lg:hidden px-5 py-2.5 bg-[#0058ba] text-white font-bold rounded-full text-sm flex items-center gap-2 mx-auto">
                        <span class="material-symbols-outlined text-sm">menu_book</span>
                        Lihat Materi
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Sidebar - Course Outline (Desktop: Livewire, Mobile: Alpine) -->
    <!-- Desktop sidebar -->
    @if($showSidebar)
    <aside class="hidden lg:block w-80 bg-[#eef1f3] h-screen sticky top-0 overflow-y-auto flex-shrink-0">
        <div class="p-6">
            <h3 class="font-headline font-bold text-lg text-[#2c2f31] mb-2">Materi Kursus</h3>
            @if($enrollment)
            <div class="flex items-center gap-2 mb-4">
                <div class="flex-1 h-1.5 rounded-full bg-[#dfe3e6] overflow-hidden">
                    <div class="h-full bg-[#00675c] rounded-full" style="width: {{ $enrollment->progress_percentage }}%"></div>
                </div>
                <span class="text-xs font-bold text-[#00675c]">{{ $enrollment->progress_percentage }}%</span>
            </div>
            @endif
            <div class="space-y-4">
                @foreach($course->modules as $module)
                <div>
                    <h4 class="text-xs font-bold text-[#595c5e] uppercase tracking-wider mb-2 px-2">
                        {{ $module->title }}
                    </h4>
                    <div class="space-y-1">
                        @foreach($module->lessons as $lesson)
                        @php $lessonDone = $completedLessonIds->contains($lesson->id); @endphp
                        @if($lessonDone)
                        <div class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left opacity-50 cursor-not-allowed bg-[#eef1f3]">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 bg-[#00675c]">
                                <span class="material-symbols-outlined text-white" style="font-size: 12px;">check</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm truncate line-through text-[#595c5e]">{{ $lesson->title }}</p>
                                <p class="text-xs text-[#595c5e]">Selesai</p>
                            </div>
                        </div>
                        @else
                        <button wire:click="selectLesson({{ $lesson->id }})"
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left transition-all duration-200
                                    {{ $currentLesson?->id === $lesson->id
                                        ? 'bg-white shadow-sm text-[#0058ba] font-semibold'
                                        : 'hover:bg-[#dfe3e6] text-[#595c5e]' }}">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-[#abadaf]"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm truncate">{{ $lesson->title }}</p>
                                @if($lesson->duration_minutes > 0)
                                <p class="text-xs opacity-60">{{ $lesson->duration_minutes }}m</p>
                                @endif
                            </div>
                            <span class="material-symbols-outlined text-sm opacity-60">{{ $lesson->type_icon }}</span>
                        </button>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </aside>
    @endif

    <!-- Mobile sidebar drawer -->
    <aside :class="mobileSidebar ? 'translate-x-0' : 'translate-x-full'"
           class="lg:hidden fixed right-0 top-0 z-50 w-80 max-w-[90vw] bg-[#eef1f3] h-screen overflow-y-auto flex-shrink-0 transition-transform duration-300 shadow-2xl">
        <div class="p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-headline font-bold text-lg text-[#2c2f31]">Materi Kursus</h3>
                <button @click="mobileSidebar = false" class="p-2 rounded-full hover:bg-[#dfe3e6] transition-colors">
                    <span class="material-symbols-outlined text-[#595c5e]">close</span>
                </button>
            </div>
            @if($enrollment)
            <div class="flex items-center gap-2 mb-4">
                <div class="flex-1 h-1.5 rounded-full bg-[#dfe3e6] overflow-hidden">
                    <div class="h-full bg-[#00675c] rounded-full" style="width: {{ $enrollment->progress_percentage }}%"></div>
                </div>
                <span class="text-xs font-bold text-[#00675c]">{{ $enrollment->progress_percentage }}%</span>
            </div>
            @endif

            <div class="space-y-4">
                @foreach($course->modules as $module)
                <div>
                    <h4 class="text-xs font-bold text-[#595c5e] uppercase tracking-wider mb-2 px-2">
                        {{ $module->title }}
                    </h4>
                    <div class="space-y-1">
                        @foreach($module->lessons as $lesson)
                        @php $lessonDone = $completedLessonIds->contains($lesson->id); @endphp
                        @if($lessonDone)
                        <div class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left opacity-50 cursor-not-allowed bg-[#eef1f3]">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 bg-[#00675c]">
                                <span class="material-symbols-outlined text-white" style="font-size: 12px;">check</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm truncate line-through text-[#595c5e]">{{ $lesson->title }}</p>
                                <p class="text-xs text-[#595c5e]">Selesai</p>
                            </div>
                        </div>
                        @else
                        <button wire:click="selectLesson({{ $lesson->id }})" @click="mobileSidebar = false"
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left transition-all duration-200
                                    {{ $currentLesson?->id === $lesson->id
                                        ? 'bg-white shadow-sm text-[#0058ba] font-semibold'
                                        : 'hover:bg-[#dfe3e6] text-[#595c5e]' }}">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-[#abadaf]"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm truncate">{{ $lesson->title }}</p>
                                @if($lesson->duration_minutes > 0)
                                <p class="text-xs opacity-60">{{ $lesson->duration_minutes }}m</p>
                                @endif
                            </div>
                            <span class="material-symbols-outlined text-sm opacity-60">{{ $lesson->type_icon }}</span>
                        </button>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </aside>

    {{-- MARK COMPLETE CONFIRMATION MODAL --}}
    @if($showCompleteConfirm)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4" wire:click.self="$set('showCompleteConfirm', false)">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl w-full sm:max-w-sm p-6 sm:p-8 text-center">
            <div class="w-10 h-1 bg-[#abadaf]/30 rounded-full mx-auto mb-4 sm:hidden"></div>
            <div class="w-14 h-14 md:w-16 md:h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-amber-500 text-2xl md:text-3xl" style="font-variation-settings: 'FILL' 1;">warning</span>
            </div>
            <h3 class="font-headline font-bold text-xl text-[#2c2f31] mb-2">Tandai Selesai?</h3>
            <p class="text-[#595c5e] text-sm mb-2">Anda yakin ingin menandai pelajaran ini sebagai selesai?</p>
            <p class="text-amber-600 text-xs font-semibold mb-6 flex items-center justify-center gap-1">
                <span class="material-symbols-outlined" style="font-size: 14px;">info</span>
                Pelajaran yang sudah selesai tidak dapat dibatalkan.
            </p>
            <div class="flex gap-3">
                <button wire:click="$set('showCompleteConfirm', false)"
                        class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors text-sm">Batal</button>
                <button wire:click="markComplete"
                        class="flex-1 py-3 bg-[#00675c] text-white font-bold rounded-full hover:bg-[#005a50] transition-colors flex items-center justify-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    Ya, Selesai
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
