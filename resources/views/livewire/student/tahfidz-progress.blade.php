<div class="p-4 md:p-8 max-w-7xl mx-auto space-y-6 md:space-y-8">

    <!-- Header -->
    <section>
        <span class="text-[#56663f] font-semibold uppercase tracking-widest text-xs block mb-2">Tahfidz</span>
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-headline font-extrabold tracking-tight text-[#2b2721]">Hafalan Saya</h2>
        <p class="text-[#6b6358] mt-1 text-sm md:text-base">Pantau perkembangan hafalan Al-Qur'an kamu.</p>
    </section>

    <!-- Tabs -->
    <div class="flex gap-1 bg-[#ebe5d8] p-1 rounded-xl overflow-x-auto">
        @foreach(['progress' => 'Progress', 'riwayat' => 'Riwayat Setoran', 'portfolio' => 'Peta Surah'] as $tab => $label)
        <button wire:click="$set('activeTab', '{{ $tab }}')"
                class="flex-1 px-4 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap
                    {{ $activeTab === $tab ? 'bg-white text-[#8a5a31] shadow-sm' : 'text-[#6b6358] hover:text-[#2b2721]' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ═══════════ PROGRESS ═══════════ --}}
    @if($activeTab === 'progress')

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#bfb5a3]/10 shadow-sm text-center">
            <div class="w-10 h-10 bg-[#cfd8bd]/30 rounded-xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-[#56663f] text-sm">auto_stories</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-[#56663f]">{{ $this->totalZiyadah }}</p>
            <p class="text-xs text-[#6b6358] mt-1">Ziyadah</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#bfb5a3]/10 shadow-sm text-center">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-[#8a5a31] text-sm">replay</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-[#8a5a31]">{{ $this->totalMurojaah }}</p>
            <p class="text-xs text-[#6b6358] mt-1">Murojaah</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#bfb5a3]/10 shadow-sm text-center">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-amber-600 text-sm">grade</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-amber-600">{{ $this->avgScore }}</p>
            <p class="text-xs text-[#6b6358] mt-1">Rata-rata Nilai</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#bfb5a3]/10 shadow-sm text-center">
            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-green-600 text-sm">local_fire_department</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-green-600">{{ $this->streakDays }}</p>
            <p class="text-xs text-[#6b6358] mt-1">Hari Berturut</p>
        </div>
    </div>

    <!-- Progress to Target -->
    <div class="relative overflow-hidden bg-gradient-to-br from-[#8a5a31] to-[#6f4826] rounded-2xl p-6 text-white">
        <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute -left-4 -bottom-4 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
        <div class="relative z-10">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div>
                    <p class="text-sm text-[#d9b98f] font-semibold uppercase tracking-widest">Target Hafalan</p>
                    @if($target)
                    <h3 class="text-xl font-headline font-extrabold mt-1">{{ $target->label ?? 'Juz 30' }}</h3>
                    <p class="text-sm text-[#d9b98f] mt-0.5">{{ $target->tingkat_kelas }} · Semester {{ $target->semester }}</p>
                    @else
                    <h3 class="text-xl font-headline font-extrabold mt-1">Juz 30 (Juz Amma)</h3>
                    <p class="text-sm text-[#d9b98f] mt-0.5">Target default</p>
                    @endif
                </div>
                <div class="bg-white/10 backdrop-blur-sm px-5 py-4 rounded-xl text-center">
                    <p class="text-3xl font-headline font-extrabold">{{ $this->progressToTarget }}%</p>
                    <p class="text-xs text-[#d9b98f]">Tercapai</p>
                </div>
            </div>
            <div class="h-3 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-[#cfd8bd] rounded-full transition-all duration-500" style="width: {{ $this->progressToTarget }}%"></div>
            </div>
            <div class="flex justify-between mt-2 text-xs text-[#d9b98f]">
                <span>{{ $this->completed_surahs->count() }} surah selesai</span>
                <span>dari {{ $target ? 'target' : '37 surah (Juz 30)' }}</span>
            </div>
        </div>
    </div>

    <!-- Recent Setoran -->
    <div>
        <h3 class="text-lg font-headline font-bold text-[#2b2721] mb-4">Setoran Terbaru</h3>
        <div class="space-y-3">
            @forelse($this->latestRecords as $record)
            <div class="bg-white rounded-xl border border-[#bfb5a3]/10 shadow-sm p-4 flex items-center gap-3 hover:shadow-md transition-shadow">
                <div class="w-10 h-10 rounded-xl {{ $record->jenis_setoran === 'ziyadah' ? 'bg-[#cfd8bd]/30' : 'bg-[#d9b98f]/20' }} flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-sm {{ $record->jenis_setoran === 'ziyadah' ? 'text-[#56663f]' : 'text-[#8a5a31]' }}">auto_stories</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-[#2b2721] truncate">{{ $record->surah?->nama_latin }}</p>
                    <p class="text-xs text-[#6b6358]">Ayat {{ $record->ayat_mulai }}-{{ $record->ayat_selesai }} · {{ $record->jenis_label }} · {{ $record->tanggal_setoran?->format('d M Y') }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-sm font-bold text-[#8a5a31]">{{ $record->score_rata_rata }}</span>
                    <span class="text-xs text-[#6b6358]">/100</span>
                    <div>
                        <span class="text-xs font-bold {{ $record->grade === 'A' ? 'text-[#56663f]' : ($record->grade === 'B' ? 'text-green-600' : ($record->grade === 'C' ? 'text-amber-600' : 'text-[#a3402c]')) }}">
                            Grade {{ $record->grade }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-[#6b6358]">
                <span class="material-symbols-outlined text-4xl mb-2 block">auto_stories</span>
                Belum ada setoran. Mulai setor hafalan ke guru tahfidz!
            </div>
            @endforelse
        </div>
    </div>

    <!-- Completed Surahs -->
    @if($this->completed_surahs->isNotEmpty())
    <div>
        <h3 class="text-lg font-headline font-bold text-[#2b2721] mb-4">Surah yang Sudah Selesai</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($this->completed_surahs as $surah)
            <div class="bg-[#cfd8bd]/30 text-[#56663f] px-3 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5">
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

    <div class="bg-white rounded-xl border border-[#bfb5a3]/10 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full min-w-[580px]">
            <thead class="bg-[#f3efe6]">
                <tr>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#6b6358] px-4 py-3">Surah & Ayat</th>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#6b6358] px-4 py-3">Jenis</th>
                    <th class="text-center text-xs font-bold uppercase tracking-wider text-[#6b6358] px-4 py-3">Kelancaran</th>
                    <th class="text-center text-xs font-bold uppercase tracking-wider text-[#6b6358] px-4 py-3">Tajwid</th>
                    <th class="text-center text-xs font-bold uppercase tracking-wider text-[#6b6358] px-4 py-3">Makhorijul</th>
                    <th class="text-center text-xs font-bold uppercase tracking-wider text-[#6b6358] px-4 py-3">Rata-rata</th>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#6b6358] px-4 py-3">Guru</th>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#6b6358] px-4 py-3">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f3efe6]">
                @forelse($allRecords as $record)
                <tr class="hover:bg-[#f3efe6] transition-colors">
                    <td class="px-4 py-3">
                        <p class="text-sm font-semibold text-[#2b2721]">{{ $record->surah?->nama_latin }}</p>
                        <p class="text-xs text-[#6b6358]">Ayat {{ $record->ayat_mulai }}-{{ $record->ayat_selesai }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $record->jenis_badge_color }}">
                            {{ $record->jenis_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-sm font-semibold text-[#2b2721]">{{ $record->score_kelancaran }}</td>
                    <td class="px-4 py-3 text-center text-sm font-semibold text-[#2b2721]">{{ $record->score_tajwid }}</td>
                    <td class="px-4 py-3 text-center text-sm font-semibold text-[#2b2721]">{{ $record->score_makhorijul_huruf }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-sm font-bold text-[#8a5a31]">{{ $record->score_rata_rata }}</span>
                        <span class="text-xs font-bold ml-1 {{ $record->grade === 'A' ? 'text-[#56663f]' : ($record->grade === 'B' ? 'text-green-600' : ($record->grade === 'C' ? 'text-amber-600' : 'text-[#a3402c]')) }}">
                            ({{ $record->grade }})
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <img src="{{ $record->instruktur?->avatar_url }}" class="w-6 h-6 rounded-full object-cover flex-shrink-0">
                            <p class="text-xs text-[#6b6358] truncate max-w-[80px]">{{ $record->instruktur?->name }}</p>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-[#6b6358]">{{ $record->tanggal_setoran?->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="py-10 text-center text-[#6b6358] text-sm">Belum ada catatan setoran.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    @endif

    {{-- ═══════════ PORTFOLIO / PETA SURAH ═══════════ --}}
    @if($activeTab === 'portfolio')

    <div>
        <h3 class="text-lg font-headline font-bold text-[#2b2721] mb-2">Peta Hafalan Al-Qur'an</h3>
        <p class="text-sm text-[#6b6358] mb-5">Visualisasi perkembangan hafalan kamu dari 114 surah.</p>

        <!-- Legend -->
        <div class="flex flex-wrap gap-4 mb-5">
            <div class="flex items-center gap-2 text-xs text-[#6b6358]">
                <div class="w-4 h-4 bg-[#cfd8bd] rounded"></div>
                <span>Selesai</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-[#6b6358]">
                <div class="w-4 h-4 bg-[#d9b98f] rounded"></div>
                <span>Sedang Proses</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-[#6b6358]">
                <div class="w-4 h-4 bg-[#ebe5d8] rounded"></div>
                <span>Belum Mulai</span>
            </div>
        </div>

        <!-- Surah Grid -->
        <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 lg:grid-cols-12 gap-1.5">
            @foreach($this->surahMap as $surah)
            <div class="aspect-square rounded-lg flex flex-col items-center justify-center text-center cursor-default group relative
                {{ $surah->state === 'completed' ? 'bg-[#cfd8bd] text-[#56663f]' : ($surah->state === 'in_progress' ? 'bg-[#d9b98f] text-white' : 'bg-[#ebe5d8] text-[#bfb5a3]') }}
                hover:scale-110 transition-transform">
                <span class="text-[10px] font-bold leading-tight">{{ $surah->nomor }}</span>
                <!-- Tooltip -->
                <div class="hidden group-hover:block absolute -top-10 left-1/2 -translate-x-1/2 bg-[#2b2721] text-white text-[10px] px-2 py-1 rounded-lg whitespace-nowrap z-10">
                    {{ $surah->nama_latin }}
                    <div class="absolute top-full left-1/2 -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-[#2b2721]"></div>
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
        <div class="bg-white p-4 rounded-xl border border-[#bfb5a3]/10 shadow-sm text-center">
            <p class="text-2xl font-headline font-extrabold text-[#56663f]">{{ $completed }}</p>
            <p class="text-xs text-[#6b6358] mt-1">Selesai</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-[#bfb5a3]/10 shadow-sm text-center">
            <p class="text-2xl font-headline font-extrabold text-[#8a5a31]">{{ $inProgress }}</p>
            <p class="text-xs text-[#6b6358] mt-1">Sedang Proses</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-[#bfb5a3]/10 shadow-sm text-center">
            <p class="text-2xl font-headline font-extrabold text-[#bfb5a3]">{{ $notStarted }}</p>
            <p class="text-xs text-[#6b6358] mt-1">Belum Mulai</p>
        </div>
    </div>

    @endif

</div>
