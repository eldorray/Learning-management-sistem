<div class="p-4 md:p-8 lg:p-12 max-w-6xl mx-auto space-y-6 md:space-y-8">
    <header>
        <span class="text-[#56663f] font-semibold uppercase tracking-widest text-xs">Belajar</span>
        <h1 class="text-2xl md:text-4xl font-headline font-extrabold tracking-tight text-[#2b2721] mt-1">Kursus Saya</h1>
        <p class="text-[#6b6358] mt-2 text-sm md:text-base">Semua kursus yang Anda ikuti, termasuk yang sudah selesai.</p>
    </header>

    <nav class="flex gap-2" aria-label="Status kursus">
        @foreach (['aktif' => ['Sedang Dipelajari', $activeCount], 'selesai' => ['Selesai', $completedCount]] as $key => [$label, $count])
            <button type="button" wire:click="$set('tab', '{{ $key }}')" aria-pressed="{{ $currentTab === $key ? 'true' : 'false' }}"
                    class="px-4 py-2 rounded-full text-sm font-semibold border transition-colors {{ $currentTab === $key ? 'bg-[#8a5a31] text-[#fbf6ee] border-[#8a5a31]' : 'bg-white text-[#6b6358] border-[#bfb5a3]/30 hover:border-[#8a5a31]/40' }}">
                {{ $label }} <span class="opacity-75">({{ $count }})</span>
            </button>
        @endforeach
    </nav>

    @if ($enrollments->isEmpty())
        <div class="bg-white rounded-xl border border-[#bfb5a3]/10 p-10 md:p-12 text-center">
            <div class="w-14 h-14 bg-[#f0e2cf] rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[#8a5a31] text-2xl" aria-hidden="true">{{ $currentTab === 'selesai' ? 'task_alt' : 'school' }}</span>
            </div>
            <h2 class="font-headline font-bold text-lg text-[#2b2721] mb-2">
                {{ $currentTab === 'selesai' ? 'Belum ada kursus yang selesai' : 'Belum ada kursus yang sedang dipelajari' }}
            </h2>
            <p class="text-[#6b6358] mb-6 text-sm">Temukan kursus baru di katalog atau masukkan kode pendaftaran dari guru.</p>
            <a href="{{ route('student.catalog') }}" class="btn-primary">
                <span class="material-symbols-outlined text-sm" aria-hidden="true">explore</span>
                Jelajahi Katalog
            </a>
        </div>
    @else
        <ul class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($enrollments as $enrollment)
                <li wire:key="enrollment-{{ $enrollment->id }}">
                    <a href="{{ route('student.learn', $enrollment->course->slug) }}"
                       class="flex gap-4 p-4 bg-white rounded-xl border border-[#bfb5a3]/15 shadow-sm hover:shadow-md hover:border-[#8a5a31]/30 transition-all">
                        <div class="w-16 h-16 md:w-20 md:h-20 rounded-xl overflow-hidden bg-gradient-to-br from-[#8a5a31] to-[#d9b98f] flex items-center justify-center flex-shrink-0">
                            @if ($enrollment->course->thumbnail)
                                <img src="{{ asset('storage/'.$enrollment->course->thumbnail) }}" alt="" class="w-full h-full object-cover">
                            @else
                                <span class="material-symbols-outlined text-white/60 text-3xl" aria-hidden="true">school</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-headline font-bold text-[#2b2721] leading-snug line-clamp-2">{{ $enrollment->course->title }}</h3>
                            <p class="text-xs text-[#6b6358] mt-0.5 truncate">
                                {{ $enrollment->course->instructor?->name }}
                                @if ($enrollment->tahunAjaran) · {{ $enrollment->tahunAjaran->nama }} @endif
                            </p>
                            @if ($enrollment->status === 'completed')
                                <p class="mt-3 text-sm font-semibold text-[#56663f] flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-base" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">verified</span>
                                    Selesai · buka untuk mengulang materi
                                </p>
                            @else
                                <div class="mt-3 flex items-center gap-3">
                                    <div class="flex-1 h-1.5 rounded-full bg-[#e4dccc] overflow-hidden" role="progressbar" aria-valuenow="{{ $enrollment->progress_percentage }}" aria-valuemin="0" aria-valuemax="100" aria-label="Progres">
                                        <div class="h-full bg-[#56663f] rounded-full" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-[#56663f]">{{ $enrollment->progress_percentage }}%</span>
                                </div>
                                <p class="mt-2 text-sm font-semibold text-[#8a5a31]">Lanjutkan belajar →</p>
                            @endif
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
