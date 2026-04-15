<div class="p-4 md:p-8 max-w-7xl mx-auto space-y-6 md:space-y-8">

    <!-- Header -->
    <section>
        <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs block mb-2">Tahfidz</span>
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-headline font-extrabold tracking-tight text-[#2c2f31]">Hafalan Saya</h2>
        <p class="text-[#595c5e] mt-1 text-sm md:text-base">Pantau perkembangan hafalan Al-Qur'an kamu.</p>
    </section>

    <!-- Tabs -->
    <div class="flex gap-1 bg-[#eef1f3] p-1 rounded-xl overflow-x-auto">
        @foreach(['progress' => 'Progress', 'riwayat' => 'Riwayat Setoran', 'portfolio' => 'Peta Surah'] as $tab => $label)
        <button wire:click="$set('activeTab', '{{ $tab }}')"
                class="flex-1 px-4 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap
                    {{ $activeTab === $tab ? 'bg-white text-[#0058ba] shadow-sm' : 'text-[#595c5e] hover:text-[#2c2f31]' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ═══════════ PROGRESS ═══════════ --}}
    @if($activeTab === 'progress')

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
            <div class="w-10 h-10 bg-[#73f2dd]/30 rounded-xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-[#00675c] text-sm">auto_stories</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-[#00675c]">{{ $this->totalZiyadah }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Ziyadah</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-[#0058ba] text-sm">replay</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-[#0058ba]">{{ $this->totalMurojaah }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Murojaah</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-amber-600 text-sm">grade</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-amber-600">{{ $this->avgScore }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Rata-rata Nilai</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-green-600 text-sm">local_fire_department</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-green-600">{{ $this->streakDays }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Hari Berturut</p>
        </div>
    </div>

    <!-- Progress to Target -->
    <div class="relative overflow-hidden bg-gradient-to-br from-[#0058ba] to-[#004da4] rounded-2xl p-6 text-white">
        <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute -left-4 -bottom-4 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div>
                    <p class="text-sm text-[#6c9fff] font-semibold uppercase tracking-widest">Target Hafalan</p>
                    @if($target)
                    <h3 class="text-xl font-headline font-extrabold mt-1">{{ $target->label ?? 'Juz 30' }}</h3>
                    <p class="text-sm text-[#6c9fff] mt-0.5">{{ $target->tingkat_kelas }} · Semester {{ $target->semester }}</p>
                    @else
                    <h3 class="text-xl font-headline font-extrabold mt-1">Juz 30 (Juz Amma)</h3>
                    <p class="text-sm text-[#6c9fff] mt-0.5">Target default</p>
                    @endif
                </div>
                <div class="bg-white/10 backdrop-blur-sm px-5 py-4 rounded-xl text-center">
                    <p class="text-3xl font-headline font-extrabold">{{ $this->progressToTarget }}%</p>
                    <p class="text-xs text-[#6c9fff]">Tercapai</p>
                </div>
            </div>
            <div class="h-3 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-[#73f2dd] rounded-full transition-all duration-500" style="width: {{ $this->progressToTarget }}%"></div>
            </div>
            <div class="flex justify-between mt-2 text-xs text-[#6c9fff]">
                <span>{{ $this->completed_surahs->count() }} surah selesai</span>
                <span>dari {{ $target ? 'target' : '37 surah (Juz 30)' }}</span>
            </div>
        </div>
    </div>

    <!-- Recent Setoran -->
    <div>
        <h3 class="text-lg font-headline font-bold text-[#2c2f31] mb-4">Setoran Terbaru</h3>
        <div class="space-y-3">
            @forelse($this->latestRecords as $record)
            <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm p-4 flex items-center gap-3 hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-xl {{ $record->jenis_setoran === 'ziyadah' ? 'bg-[#73f2dd]/30' : 'bg-[#6c9fff]/20' }} flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-sm {{ $record->jenis_setoran === 'ziyadah' ? 'text-[#00675c]' : 'text-[#0058ba]' }}">auto_stories</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-[#2c2f31] truncate">{{ $record->surah?->nama_latin }}</p>
                    <p class="text-xs text-[#595c5e]">Ayat {{ $record->ayat_mulai }}-{{ $record->ayat_selesai }} · {{ $record->jenis_label }} · {{ $record->tanggal_setoran?->format('d M Y') }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-sm font-bold text-[#0058ba]">{{ $record->score_rata_rata }}</span>
                    <span class="text-xs text-[#595c5e]">/100</span>
                    <div>
                        <span class="text-xs font-bold {{ $record->grade === 'A' ? 'text-[#00675c]' : ($record->grade === 'B' ? 'text-green-600' : ($record->grade === 'C' ? 'text-amber-600' : 'text-[#b31b25]')) }}">
                            Grade {{ $record->grade }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-[#595c5e]">
                <span class="material-symbols-outlined text-4xl mb-2 block">auto_stories</span>
                Belum ada setoran. Mulai setor hafalan ke guru tahfidz!
            </div>
            @endforelse
        </div>
    </div>

    <!-- Completed Surahs -->
    @if($this->completed_surahs->isNotEmpty())
    <div>
        <h3 class="text-lg font-headline font-bold text-[#2c2f31] mb-4">Surah yang Sudah Selesai</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($this->completed_surahs as $surah)
            <div class="bg-[#73f2dd]/30 text-[#00675c] px-3 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5">
                <span class="material-symbols-outlined text-xs" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                {{ $surah->nama_latin }}
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @endif

    {{-- ═══════════ RIWAYAT SETORAN ═══════════ --}}
    @if($activeTab === 'riwayat')

    <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full min-w-[580px]">
            <thead class="bg-[#f5f7f9]">
                <tr>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Surah & Ayat</th>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Jenis</th>
                    <th class="text-center text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Kelancaran</th>
                    <th class="text-center text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Tajwid</th>
                    <th class="text-center text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Makhorijul</th>
                    <th class="text-center text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Rata-rata</th>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Guru</th>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f5f7f9]">
                @forelse($allRecords as $record)
                <tr class="hover:bg-[#f5f7f9] transition-colors">
                    <td class="px-4 py-3">
                        <p class="text-sm font-semibold text-[#2c2f31]">{{ $record->surah?->nama_latin }}</p>
                        <p class="text-xs text-[#595c5e]">Ayat {{ $record->ayat_mulai }}-{{ $record->ayat_selesai }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $record->jenis_badge_color }}">
                            {{ $record->jenis_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-sm font-semibold text-[#2c2f31]">{{ $record->score_kelancaran }}</td>
                    <td class="px-4 py-3 text-center text-sm font-semibold text-[#2c2f31]">{{ $record->score_tajwid }}</td>
                    <td class="px-4 py-3 text-center text-sm font-semibold text-[#2c2f31]">{{ $record->score_makhorijul_huruf }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-sm font-bold text-[#0058ba]">{{ $record->score_rata_rata }}</span>
                        <span class="text-xs font-bold ml-1 {{ $record->grade === 'A' ? 'text-[#00675c]' : ($record->grade === 'B' ? 'text-green-600' : ($record->grade === 'C' ? 'text-amber-600' : 'text-[#b31b25]')) }}">
                            ({{ $record->grade }})
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <img src="{{ $record->instruktur?->avatar_url }}" class="w-6 h-6 rounded-full object-cover flex-shrink-0">
                            <p class="text-xs text-[#595c5e] truncate max-w-[80px]">{{ $record->instruktur?->name }}</p>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-[#595c5e]">{{ $record->tanggal_setoran?->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="py-10 text-center text-[#595c5e] text-sm">Belum ada catatan setoran.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    @endif

    {{-- ═══════════ PORTFOLIO / PETA SURAH ═══════════ --}}
    @if($activeTab === 'portfolio')

    <div>
        <h3 class="text-lg font-headline font-bold text-[#2c2f31] mb-2">Peta Hafalan Al-Qur'an</h3>
        <p class="text-sm text-[#595c5e] mb-5">Visualisasi perkembangan hafalan kamu dari 114 surah.</p>

        <!-- Legend -->
        <div class="flex flex-wrap gap-4 mb-5">
            <div class="flex items-center gap-2 text-xs text-[#595c5e]">
                <div class="w-4 h-4 bg-[#73f2dd] rounded"></div>
                <span>Selesai</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-[#595c5e]">
                <div class="w-4 h-4 bg-[#6c9fff] rounded"></div>
                <span>Sedang Proses</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-[#595c5e]">
                <div class="w-4 h-4 bg-[#eef1f3] rounded"></div>
                <span>Belum Mulai</span>
            </div>
        </div>

        <!-- Surah Grid -->
        <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 lg:grid-cols-12 gap-1.5">
            @foreach($this->surahMap as $surah)
            <div class="aspect-square rounded-lg flex flex-col items-center justify-center text-center cursor-default group relative
                {{ $surah->state === 'completed' ? 'bg-[#73f2dd] text-[#00675c]' : ($surah->state === 'in_progress' ? 'bg-[#6c9fff] text-white' : 'bg-[#eef1f3] text-[#abadaf]') }}
                hover:scale-110 transition-transform">
                <span class="text-[10px] font-bold leading-tight">{{ $surah->nomor }}</span>
                <!-- Tooltip -->
                <div class="hidden group-hover:block absolute -top-10 left-1/2 -translate-x-1/2 bg-[#2c2f31] text-white text-[10px] px-2 py-1 rounded-lg whitespace-nowrap z-10">
                    {{ $surah->nama_latin }}
                    <div class="absolute top-full left-1/2 -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-[#2c2f31]"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-3 gap-4">
        @php
            $completed = $this->surahMap->where('state', 'completed')->count();
            $inProgress = $this->surahMap->where('state', 'in_progress')->count();
            $notStarted = $this->surahMap->where('state', 'not_started')->count();
        @endphp
        <div class="bg-white p-4 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
            <p class="text-2xl font-headline font-extrabold text-[#00675c]">{{ $completed }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Selesai</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
            <p class="text-2xl font-headline font-extrabold text-[#0058ba]">{{ $inProgress }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Sedang Proses</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
            <p class="text-2xl font-headline font-extrabold text-[#abadaf]">{{ $notStarted }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Belum Mulai</p>
        </div>
    </div>

    @endif

</div>
