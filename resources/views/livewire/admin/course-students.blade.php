<div class="p-4 md:p-8 max-w-7xl mx-auto space-y-6 md:space-y-8">

    <!-- Header -->
    <section class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <a href="{{ route('admin.courses') }}" class="p-2 rounded-full hover:bg-[#eef1f3] transition-colors">
                    <span class="material-symbols-outlined text-[#595c5e]">arrow_back</span>
                </a>
                <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs">Manajemen Kursus</span>
            </div>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-headline font-extrabold tracking-tight text-[#2c2f31]">{{ $course->title }}</h2>
            <p class="text-[#595c5e] mt-1">Siswa terdaftar & progres pembelajaran</p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <div class="bg-white px-5 py-3 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
                <p class="text-xs text-[#595c5e]">Terdaftar</p>
                <p class="text-xl font-headline font-bold text-[#0058ba]">{{ $totalEnrolled }}</p>
            </div>
            <div class="bg-white px-5 py-3 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
                <p class="text-xs text-[#595c5e]">Selesai</p>
                <p class="text-xl font-headline font-bold text-[#00675c]">{{ $completedCount }}</p>
            </div>
            <div class="bg-white px-5 py-3 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
                <p class="text-xs text-[#595c5e]">Rata-rata</p>
                <p class="text-xl font-headline font-bold text-amber-600">{{ round($avgProgress) }}%</p>
            </div>
            @if($pendingEssayCount > 0)
            <div class="bg-white px-5 py-3 rounded-xl border border-amber-200 shadow-sm text-center relative">
                <div class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-amber-500 rounded-full flex items-center justify-center">
                    <span class="text-white text-[10px] font-bold">!</span>
                </div>
                <p class="text-xs text-[#595c5e]">Esai Pending</p>
                <p class="text-xl font-headline font-bold text-amber-600">{{ $pendingEssayCount }}</p>
            </div>
            @endif
        </div>
    </section>

    <!-- Flash Message -->
    @if(session('success'))
    <div class="bg-[#73f2dd]/30 border border-[#00675c]/20 text-[#00675c] px-4 py-3 rounded-xl flex items-center gap-3 animate-fade-in">
        <span class="material-symbols-outlined">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-3">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#595c5e] text-sm">search</span>
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="Cari nama, email, atau NIS siswa..."
                   class="w-full pl-12 pr-4 py-3 bg-white border border-[#abadaf]/20 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
        </div>
        <select wire:model.live="filterStatus"
                class="px-4 py-3 bg-white border border-[#abadaf]/20 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
            <option value="">Semua Status</option>
            <option value="active">Sedang Belajar</option>
            <option value="completed">Selesai</option>
        </select>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
        @if($enrollments->isEmpty())
        <div class="p-16 text-center">
            <div class="w-16 h-16 bg-[#eef1f3] rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[#595c5e] text-3xl">group_off</span>
            </div>
            <h3 class="font-headline font-bold text-xl text-[#2c2f31] mb-2">Belum Ada Siswa</h3>
            <p class="text-[#595c5e]">Belum ada siswa yang mendaftar ke kursus ini.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[500px]">
                <thead>
                    <tr class="bg-[#f5f7f9]">
                        <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#595c5e]">Siswa</th>
                        <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#595c5e] hidden md:table-cell">Progres</th>
                        <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#595c5e] hidden lg:table-cell">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#595c5e] hidden lg:table-cell">Terdaftar</th>
                        <th class="text-right px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#595c5e]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f5f7f9]">
                    @foreach($enrollments as $enrollment)
                    <tr class="hover:bg-[#f5f7f9] transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $enrollment->user->avatar_url }}"
                                     alt="{{ $enrollment->user->name }}"
                                     class="w-10 h-10 rounded-xl object-cover">
                                <div class="min-w-0">
                                    <p class="font-semibold text-[#2c2f31] text-sm truncate group-hover:text-[#0058ba] transition-colors">{{ $enrollment->user->name }}</p>
                                    <p class="text-xs text-[#595c5e] truncate">{{ $enrollment->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <div class="flex items-center gap-3">
                                <div class="w-24 h-2 rounded-full bg-[#e5e9eb] overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500
                                        {{ $enrollment->progress_percentage >= 100 ? 'bg-[#00675c]' : ($enrollment->progress_percentage >= 50 ? 'bg-[#0058ba]' : 'bg-amber-500') }}"
                                         style="width: {{ $enrollment->progress_percentage }}%"></div>
                                </div>
                                <span class="text-sm font-bold text-[#2c2f31] w-10">{{ $enrollment->progress_percentage }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                {{ $enrollment->status === 'completed' ? 'bg-[#73f2dd]/30 text-[#00675c]' : 'bg-[#0058ba]/10 text-[#0058ba]' }}">
                                {{ $enrollment->status === 'completed' ? 'Selesai' : 'Belajar' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <span class="text-sm text-[#595c5e]">{{ $enrollment->created_at->format('d M Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="viewDetail({{ $enrollment->user->id }})"
                                        class="p-2 text-[#595c5e] hover:text-[#0058ba] hover:bg-blue-50 rounded-lg transition-colors"
                                        title="Lihat Progres">
                                    <span class="material-symbols-outlined text-sm">analytics</span>
                                </button>
                                <button wire:click="confirmRemove({{ $enrollment->user->id }})"
                                        class="p-2 text-[#595c5e] hover:text-[#b31b25] hover:bg-red-50 rounded-lg transition-colors"
                                        title="Keluarkan Siswa">
                                    <span class="material-symbols-outlined text-sm">person_remove</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-[#f5f7f9]">
            {{ $enrollments->links() }}
        </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════════════════════
         STUDENT DETAIL & PROGRESS MODAL
         ════════════════════════════════════════════════════════ --}}
    @if($showDetailModal && $viewingStudent)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4" wire:click.self="$set('showDetailModal', false)">
        <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-2xl w-full sm:max-w-2xl max-h-[92vh] overflow-y-auto">

            <!-- Header -->
            <div class="bg-gradient-to-br from-[#0058ba] to-[#004da4] p-6 rounded-t-xl relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-white rounded-full"></div>
                </div>
                <div class="relative flex items-center gap-4">
                    <img src="{{ $viewingStudent->avatar_url }}" class="w-14 h-14 rounded-2xl object-cover border-2 border-white/30">
                    <div>
                        <h3 class="font-headline font-bold text-xl text-white">{{ $viewingStudent->name }}</h3>
                        <p class="text-white/70 text-sm">{{ $viewingStudent->email }}</p>
                    </div>
                </div>
                <button wire:click="$set('showDetailModal', false)"
                        class="absolute top-4 right-4 p-1.5 rounded-full bg-white/10 hover:bg-white/20 transition-colors">
                    <span class="material-symbols-outlined text-white text-sm">close</span>
                </button>
            </div>

            <!-- Lesson Progress -->
            <div class="p-6">
                <h4 class="font-headline font-bold text-lg text-[#2c2f31] mb-4">Progres per Pelajaran</h4>
                <div class="space-y-2">
                    @foreach($course->modules as $module)
                    <div class="mb-4">
                        <p class="text-xs font-bold text-[#595c5e] uppercase tracking-wider mb-2 px-1">{{ $module->title }}</p>
                        @foreach($module->lessons as $lesson)
                        @php
                            $isCompleted = $studentProgress->get($lesson->id, false);
                        @endphp
                        <div class="flex items-center justify-between py-2.5 px-3 rounded-lg hover:bg-[#f5f7f9] transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0
                                    {{ $isCompleted ? 'bg-[#00675c]' : 'border-2 border-[#abadaf]' }}">
                                    @if($isCompleted)
                                    <span class="material-symbols-outlined text-white" style="font-size: 12px;">check</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium {{ $isCompleted ? 'text-[#2c2f31]' : 'text-[#595c5e]' }}">{{ $lesson->title }}</p>
                                    <p class="text-xs text-[#595c5e]">
                                        {{ match($lesson->type) { 'video' => '🎬 Video', 'quiz' => '📝 Kuis', default => '📄 Artikel' } }}
                                    </p>
                                </div>
                            </div>

                            @if($lesson->type === 'quiz' && isset($studentQuizResults[$lesson->id]))
                            @php $qr = $studentQuizResults[$lesson->id]; @endphp
                            <div class="flex items-center gap-2">
                                @if($qr['ungraded_essays'] > 0)
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 flex items-center gap-1">
                                    <span class="material-symbols-outlined" style="font-size:12px">rate_review</span>
                                    {{ $qr['ungraded_essays'] }} esai
                                </span>
                                @endif
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full
                                    {{ ($qr['total_points'] > 0 && ($qr['earned_points'] / $qr['total_points']) >= 0.6) ? 'bg-[#73f2dd]/30 text-[#00675c]' : 'bg-red-50 text-[#b31b25]' }}">
                                    {{ $qr['earned_points'] }}/{{ $qr['total_points'] }} poin
                                </span>
                                <button wire:click="viewAnswers({{ $viewingStudent->id }}, {{ $lesson->id }})"
                                        class="p-1.5 text-[#0058ba] hover:bg-blue-50 rounded-lg transition-colors"
                                        title="Lihat Jawaban">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">assignment</span>
                                </button>
                            </div>
                            @elseif($lesson->type === 'quiz')
                            <span class="text-xs text-[#595c5e] italic">Belum dikerjakan</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="p-6 border-t border-[#eef1f3]">
                <button wire:click="$set('showDetailModal', false)"
                        class="w-full py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════════════════════════
         QUIZ ANSWERS DETAIL MODAL
         ════════════════════════════════════════════════════════ --}}
    @if($showAnswersModal && $answersLesson && $answersStudent)
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" wire:click.self="$set('showAnswersModal', false)">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">

            <!-- Header -->
            <div class="p-6 border-b border-[#eef1f3] flex items-center justify-between sticky top-0 bg-white z-10">
                <div>
                    <p class="text-xs text-[#595c5e] font-semibold uppercase tracking-wider">Jawaban Kuis</p>
                    <h3 class="font-headline font-bold text-lg text-[#2c2f31]">{{ $answersLesson->title }}</h3>
                    <p class="text-sm text-[#595c5e]">{{ $answersStudent->name }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($hasEssayQuestions)
                    <button wire:click="gradeAllEssays" wire:confirm="Simpan semua nilai esai?"
                            class="px-4 py-2 bg-gradient-to-br from-[#00675c] to-[#005a50] text-white font-bold rounded-full text-xs hover:scale-[1.02] transition-transform shadow-sm flex items-center gap-1.5">
                        <span wire:loading.remove wire:target="gradeAllEssays" class="material-symbols-outlined text-sm">grading</span>
                        <span wire:loading wire:target="gradeAllEssays" class="inline-block animate-spin text-sm">⟳</span>
                        Simpan Semua Nilai
                    </button>
                    @endif
                    <button wire:click="$set('showAnswersModal', false)" class="p-2 rounded-full hover:bg-[#eef1f3] transition-colors">
                        <span class="material-symbols-outlined text-[#595c5e]">close</span>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-5">
                @foreach($answersLesson->quizQuestions as $qIndex => $question)
                @php
                    $answer = $quizAnswersDetail->get($question->id);
                    $selectedOptionId = $answer?->quiz_option_id;
                    $isCorrect = $answer?->is_correct ?? false;
                    $isEssay = $question->type === 'essay';
                    $isGraded = $answer?->graded_at !== null;
                @endphp
                <div class="bg-[#f5f7f9] rounded-xl p-4
                    {{ $isEssay
                        ? ($isGraded ? 'ring-1 ring-[#0058ba]/20' : ($answer?->essay_answer ? 'ring-2 ring-amber-400/40' : ''))
                        : ($answer ? ($isCorrect ? 'ring-1 ring-[#00675c]/20' : 'ring-1 ring-[#b31b25]/20') : '') }}">

                    <!-- Question -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 bg-[#0058ba] text-white text-xs font-bold rounded-full flex items-center justify-center">{{ $qIndex + 1 }}</span>
                            <span class="text-xs font-semibold text-[#595c5e]">{{ $question->getTypeLabel() }} · {{ $question->points }} poin</span>
                        </div>
                        @if($isEssay)
                            @if($isGraded)
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-[#0058ba]/10 text-[#0058ba] flex items-center gap-1">
                                <span class="material-symbols-outlined" style="font-size:12px">check_circle</span>
                                Dinilai · {{ $answer->points_earned }}p
                            </span>
                            @elseif($answer?->essay_answer)
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 flex items-center gap-1 animate-pulse">
                                <span class="material-symbols-outlined" style="font-size:12px">rate_review</span>
                                Menunggu Penilaian
                            </span>
                            @else
                            <span class="text-xs text-[#595c5e] italic">Tidak dijawab</span>
                            @endif
                        @else
                            @if($answer)
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $isCorrect ? 'bg-[#73f2dd]/30 text-[#00675c]' : 'bg-red-50 text-[#b31b25]' }}">
                                {{ $isCorrect ? '✓ Benar' : '✗ Salah' }} · {{ $answer->points_earned }}p
                            </span>
                            @else
                            <span class="text-xs text-[#595c5e] italic">Tidak dijawab</span>
                            @endif
                        @endif
                    </div>

                    <p class="text-sm font-semibold text-[#2c2f31] mb-3">{{ $question->question }}</p>

                    <!-- Options (for non-essay) -->
                    @if(!$isEssay)
                    <div class="space-y-1.5">
                        @foreach($question->options as $option)
                        @php
                            $isSelected = $selectedOptionId == $option->id;
                            $isCorrectOption = $option->is_correct;
                        @endphp
                        <div class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm
                            {{ $isCorrectOption ? 'bg-[#73f2dd]/20 border border-[#00675c]/20' : ($isSelected && !$isCorrectOption ? 'bg-red-50 border border-[#b31b25]/20' : 'bg-white border border-transparent') }}">
                            <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0
                                {{ $isSelected ? ($isCorrectOption ? 'border-[#00675c] bg-[#00675c]' : 'border-[#b31b25] bg-[#b31b25]') : 'border-[#abadaf]' }}">
                                @if($isSelected)
                                <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                @endif
                            </div>
                            <span class="{{ $isCorrectOption ? 'text-[#00675c] font-semibold' : ($isSelected ? 'text-[#b31b25]' : 'text-[#595c5e]') }}">
                                {{ $option->option_text }}
                            </span>
                            @if($isCorrectOption)
                            <span class="material-symbols-outlined text-[#00675c]" style="font-size: 14px; font-variation-settings: 'FILL' 1;">check_circle</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Essay Answer & Grading Form -->
                    @if($isEssay && $answer?->essay_answer)
                    <div class="space-y-3 mt-2">
                        {{-- Student's essay answer --}}
                        <div class="bg-white border border-[#abadaf]/10 rounded-lg p-4">
                            <p class="text-xs font-semibold text-[#595c5e] mb-2 flex items-center gap-1">
                                <span class="material-symbols-outlined" style="font-size:14px">description</span>
                                Jawaban Siswa:
                            </p>
                            <p class="text-sm text-[#2c2f31] leading-relaxed whitespace-pre-wrap">{{ $answer->essay_answer }}</p>
                        </div>

                        {{-- Grading Form --}}
                        <div class="bg-white border-2 border-[#0058ba]/10 rounded-xl p-4 space-y-3">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="material-symbols-outlined text-[#0058ba] text-sm">grading</span>
                                <span class="text-sm font-bold text-[#0058ba]">Penilaian Esai</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-[120px_1fr] gap-3">
                                {{-- Score input --}}
                                <div>
                                    <label class="block text-xs font-semibold text-[#2c2f31] mb-1">Skor</label>
                                    <div class="flex items-center gap-2">
                                        <input type="number"
                                               wire:model="essayScores.{{ $answer->id }}"
                                               min="0" max="{{ $question->points }}"
                                               class="w-full px-3 py-2 bg-[#eef1f3] border-none rounded-lg text-sm font-bold text-center focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                                        <span class="text-xs text-[#595c5e] flex-shrink-0">/{{ $question->points }}</span>
                                    </div>
                                </div>

                                {{-- Feedback textarea --}}
                                <div>
                                    <label class="block text-xs font-semibold text-[#2c2f31] mb-1">Feedback <span class="font-normal text-[#595c5e]">(opsional)</span></label>
                                    <textarea wire:model="essayFeedback.{{ $answer->id }}"
                                              rows="2"
                                              placeholder="Berikan catatan untuk siswa..."
                                              class="w-full px-3 py-2 bg-[#eef1f3] border-none rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 resize-none"></textarea>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    @if($isGraded)
                                    <p class="text-[10px] text-[#595c5e]">
                                        Terakhir dinilai oleh {{ $answer->grader?->name ?? 'Unknown' }}
                                        · {{ $answer->graded_at->diffForHumans() }}
                                    </p>
                                    @endif
                                </div>
                                <button wire:click="gradeEssay({{ $answer->id }})"
                                        class="px-4 py-2 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-white font-bold rounded-full text-xs hover:scale-[1.02] transition-transform shadow-sm flex items-center gap-1.5">
                                    <span wire:loading.remove wire:target="gradeEssay({{ $answer->id }})" class="material-symbols-outlined" style="font-size:14px">save</span>
                                    <span wire:loading wire:target="gradeEssay({{ $answer->id }})" class="inline-block animate-spin text-xs">⟳</span>
                                    Simpan Nilai
                                </button>
                            </div>
                        </div>
                    </div>
                    @elseif($isEssay && !$answer?->essay_answer)
                    <div class="bg-[#eef1f3] rounded-lg p-3 mt-2 text-center">
                        <p class="text-xs text-[#595c5e] italic">Siswa belum menjawab pertanyaan ini.</p>
                    </div>
                    @endif

                    <!-- Explanation -->
                    @if($question->explanation)
                    <div class="bg-[#0058ba]/5 border border-[#0058ba]/10 rounded-lg p-3 mt-3">
                        <p class="text-xs font-semibold text-[#0058ba] flex items-center gap-1 mb-1">
                            <span class="material-symbols-outlined" style="font-size: 14px;">lightbulb</span> Penjelasan
                        </p>
                        <p class="text-xs text-[#595c5e]">{{ $question->explanation }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <div class="p-6 border-t border-[#eef1f3] flex items-center gap-3">
                @if($hasEssayQuestions)
                <button wire:click="gradeAllEssays" wire:confirm="Simpan semua nilai esai?"
                        class="flex-1 py-3 bg-gradient-to-br from-[#00675c] to-[#005a50] text-white font-bold rounded-full hover:scale-[1.01] transition-transform shadow-sm flex items-center justify-center gap-2 text-sm">
                    <span class="material-symbols-outlined text-sm">grading</span>
                    Simpan Semua Nilai Esai
                </button>
                @endif
                <button wire:click="$set('showAnswersModal', false)"
                        class="{{ $hasEssayQuestions ? 'flex-1' : 'w-full' }} py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════════════════════════
         REMOVE STUDENT CONFIRMATION
         ════════════════════════════════════════════════════════ --}}
    @if($showRemoveModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="$set('showRemoveModal', false)">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-8 mx-4 text-center">
            <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[#b31b25] text-3xl">person_remove</span>
            </div>
            <h3 class="font-headline font-bold text-xl text-[#2c2f31] mb-2">Keluarkan Siswa?</h3>
            <p class="text-[#595c5e] text-sm mb-6">Siswa akan dikeluarkan dari kursus ini. Semua progres dan jawaban kuis akan dihapus.</p>
            <div class="flex gap-3">
                <button wire:click="$set('showRemoveModal', false)"
                        class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors">Batal</button>
                <button wire:click="removeStudent"
                        class="flex-1 py-3 bg-[#b31b25] text-white font-bold rounded-full hover:bg-[#9f0519] transition-colors">Keluarkan</button>
            </div>
        </div>
    </div>
    @endif

</div>
