<div class="p-4 md:p-8 max-w-7xl mx-auto space-y-6 md:space-y-8">

    <!-- Header -->
    <section>
        <span class="text-[#56663f] font-semibold uppercase tracking-widest text-xs block mb-2">Portal Orang Tua</span>
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-headline font-extrabold tracking-tight text-[#2b2721]">
            Pantau Perkembangan Anak
        </h2>
        <p class="text-[#6b6358] mt-1 text-sm md:text-base">Lihat hasil belajar dan hafalan anak Anda secara real-time.</p>
    </section>

    {{-- No children linked --}}
    @if($this->children->isEmpty())
    <div class="py-20 text-center bg-white rounded-2xl border border-[#bfb5a3]/10 shadow-sm">
        <span class="material-symbols-outlined text-6xl text-[#bfb5a3] block mb-4">family_restroom</span>
        <h3 class="font-headline font-bold text-xl text-[#2b2721] mb-2">Belum Ada Anak Terdaftar</h3>
        <p class="text-[#6b6358] text-sm max-w-sm mx-auto">Hubungi admin sekolah untuk menghubungkan akun Anda dengan data siswa.</p>
    </div>
    @else

    <!-- Child Selector -->
    @if($this->children->count() > 1)
    <div class="flex gap-3 overflow-x-auto pb-1">
        @foreach($this->children as $child)
        <button wire:click="selectChild({{ $child->id }})"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl border-2 flex-shrink-0 transition-all
                    {{ $selectedChildId === $child->id
                        ? 'bg-[#8a5a31] border-[#8a5a31] text-white shadow-lg shadow-blue-500/20'
                        : 'bg-white border-[#bfb5a3]/20 text-[#2b2721] hover:border-[#8a5a31]/30' }}">
            <img src="{{ $child->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
            <div class="text-left">
                <p class="text-sm font-bold truncate max-w-[120px]">{{ $child->name }}</p>
                <p class="text-xs {{ $selectedChildId === $child->id ? 'text-[#d9b98f]' : 'text-[#6b6358]' }}">
                    {{ $child->pivot->hubungan === 'wali' ? 'Wali' : 'Anak' }}
                </p>
            </div>
        </button>
        @endforeach
    </div>
    @endif

    @if($this->selectedChild)
    @php $child = $this->selectedChild; @endphp

    <!-- Child Profile Banner -->
    <div class="relative overflow-hidden bg-gradient-to-br from-[#8a5a31] to-[#6f4826] rounded-2xl p-5 md:p-6 text-white">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative z-10 flex items-center gap-4 md:gap-6">
            <img src="{{ $child->avatar_url }}" class="w-16 h-16 md:w-20 md:h-20 rounded-2xl object-cover border-2 border-white/30 flex-shrink-0">
            <div class="flex-1 min-w-0">
                <p class="text-sm text-[#d9b98f] font-semibold uppercase tracking-widest">Profil Anak</p>
                <h3 class="text-xl md:text-2xl font-headline font-extrabold mt-0.5 truncate">{{ $child->name }}</h3>
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2 text-sm text-[#d9b98f]">
                    @if($child->class_group)
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">school</span>
                        {{ $child->class_group }}
                    </span>
                    @endif
                    @if($child->nis)
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">badge</span>
                        NIS: {{ $child->nis }}
                    </span>
                    @endif
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">bolt</span>
                        {{ number_format($child->xp_points) }} XP
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 bg-[#ebe5d8] p-1 rounded-xl overflow-x-auto">
        @foreach(['overview' => 'Ringkasan', 'kursus' => 'Kursus', 'tahfidz' => 'Tahfidz', 'profil' => 'Profil'] as $tab => $label)
        <button wire:click="$set('activeTab', '{{ $tab }}')"
                class="flex-1 px-4 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap
                    {{ $activeTab === $tab ? 'bg-white text-[#8a5a31] shadow-sm' : 'text-[#6b6358] hover:text-[#2b2721]' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ═══════════ OVERVIEW ═══════════ --}}
    @if($activeTab === 'overview')

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
        <!-- Kursus Progress -->
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#bfb5a3]/10 shadow-sm">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-[#8a5a31] text-sm">library_books</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-[#8a5a31]">{{ $enrollmentStats['total'] }}</p>
            <p class="text-xs text-[#6b6358] mt-1">Kursus Diikuti</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#bfb5a3]/10 shadow-sm">
            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-green-600 text-sm">task_alt</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-green-600">{{ $enrollmentStats['avg_progress'] }}%</p>
            <p class="text-xs text-[#6b6358] mt-1">Rata-rata Progres</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#bfb5a3]/10 shadow-sm">
            <div class="w-10 h-10 bg-[#cfd8bd]/30 rounded-xl flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-[#56663f] text-sm">auto_stories</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-[#56663f]">{{ $tahfidzStats['total_setoran'] }}</p>
            <p class="text-xs text-[#6b6358] mt-1">Total Setoran</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#bfb5a3]/10 shadow-sm">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-amber-600 text-sm">local_fire_department</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-amber-600">{{ $tahfidzStats['streak'] }}</p>
            <p class="text-xs text-[#6b6358] mt-1">Hari Berturut</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kursus Terkini -->
        <div>
            <h3 class="text-lg font-headline font-bold text-[#2b2721] mb-4">Progres Kursus</h3>
            <div class="bg-white rounded-xl border border-[#bfb5a3]/10 shadow-sm overflow-hidden">
                @if($child->enrollments->isEmpty())
                <div class="p-8 text-center text-[#6b6358] text-sm">Belum ada kursus yang diikuti.</div>
                @else
                <div class="divide-y divide-[#f3efe6]">
                    @foreach($child->enrollments->take(5) as $enrollment)
                    <div class="p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-[#ebe5d8] rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-[#8a5a31] text-sm">library_books</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-[#2b2721] truncate">{{ $enrollment->course?->title }}</p>
                                <div class="flex items-center justify-between mt-1.5 gap-2">
                                    <div class="flex-1 h-1.5 bg-[#ebe5d8] rounded-full overflow-hidden">
                                        <div class="h-full bg-[#8a5a31] rounded-full transition-all"
                                             style="width: {{ $enrollment->progress_percentage }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-[#8a5a31] flex-shrink-0">{{ $enrollment->progress_percentage }}%</span>
                                </div>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-xs text-[#6b6358]">{{ $enrollment->course?->category }}</span>
                                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold
                                        {{ $enrollment->status === 'completed' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-[#8a5a31]' }}">
                                        {{ $enrollment->status === 'completed' ? 'Selesai' : 'Aktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Setoran Tahfidz Terkini -->
        <div>
            <h3 class="text-lg font-headline font-bold text-[#2b2721] mb-4">Setoran Tahfidz Terkini</h3>
            <div class="bg-white rounded-xl border border-[#bfb5a3]/10 shadow-sm overflow-hidden">
                @if($this->recentTahfidz->isEmpty())
                <div class="p-8 text-center text-[#6b6358] text-sm">Belum ada catatan setoran.</div>
                @else
                <div class="divide-y divide-[#f3efe6]">
                    @foreach($this->recentTahfidz as $record)
                    <div class="flex items-center gap-3 p-4">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
                            {{ $record->jenis_setoran === 'ziyadah' ? 'bg-[#cfd8bd]/30' : 'bg-[#d9b98f]/20' }}">
                            <span class="material-symbols-outlined text-sm
                                {{ $record->jenis_setoran === 'ziyadah' ? 'text-[#56663f]' : 'text-[#8a5a31]' }}">
                                auto_stories
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-[#2b2721] truncate">
                                {{ $record->surah?->nama_latin }} ({{ $record->ayat_mulai }}-{{ $record->ayat_selesai }})
                            </p>
                            <p class="text-xs text-[#6b6358]">
                                {{ $record->jenis_label }} · {{ $record->tanggal_setoran?->format('d M Y') }}
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-[#8a5a31]">{{ $record->score_rata_rata }}</p>
                            <p class="text-xs font-bold
                                {{ $record->grade === 'A' ? 'text-[#56663f]' : ($record->grade === 'B' ? 'text-green-600' : 'text-amber-600') }}">
                                {{ $record->grade }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tahfidz Score Trend (6 bulan) -->
    @if($this->tahfidzMonthlyTrend->isNotEmpty())
    <div>
        <h3 class="text-lg font-headline font-bold text-[#2b2721] mb-4">Tren Setoran & Nilai (6 Bulan)</h3>
        <div class="bg-white p-6 rounded-xl border border-[#bfb5a3]/10 shadow-sm">
            @php $maxTotal = $this->tahfidzMonthlyTrend->max('total') ?: 1; @endphp
            <div class="flex items-end gap-3 h-40 justify-around">
                @foreach($this->tahfidzMonthlyTrend as $item)
                <div class="flex flex-col items-center gap-1.5 flex-1">
                    <span class="text-xs font-bold text-[#56663f]">{{ $item->avg_score }}</span>
                    <div class="w-full relative rounded-t-lg overflow-hidden"
                         style="height: {{ max(8, round(($item->total / $maxTotal) * 120)) }}px">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#8a5a31] to-[#d9b98f] rounded-t-lg"></div>
                    </div>
                    <span class="text-[10px] text-[#6b6358] text-center leading-tight">{{ $item->label }}</span>
                    <span class="text-[10px] font-bold text-[#6b6358]">{{ $item->total }}x</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @endif

    {{-- ═══════════ KURSUS ═══════════ --}}
    @if($activeTab === 'kursus')

    <div class="flex items-center justify-between mb-2">
        <h3 class="text-lg font-headline font-bold text-[#2b2721]">Kursus yang Diikuti</h3>
        <div class="flex items-center gap-2 text-xs font-semibold text-[#6b6358] bg-[#ebe5d8] px-3 py-1.5 rounded-full">
            <span class="material-symbols-outlined text-sm">visibility</span>
            Hanya Lihat
        </div>
    </div>

    @if($child->enrollments->isEmpty())
    <div class="py-16 text-center bg-white rounded-2xl border border-[#bfb5a3]/10 shadow-sm">
        <span class="material-symbols-outlined text-5xl text-[#bfb5a3] block mb-3">library_books</span>
        <p class="text-[#6b6358] text-sm">Anak Anda belum mengikuti kursus apapun.</p>
    </div>
    @else

    <!-- Overall Progress -->
    <div class="bg-gradient-to-br from-[#8a5a31] to-[#6f4826] rounded-2xl p-5 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-sm text-[#d9b98f] font-semibold">Progres Keseluruhan</p>
                <p class="text-3xl font-headline font-extrabold mt-1">{{ $enrollmentStats['avg_progress'] }}%</p>
                <p class="text-sm text-[#d9b98f] mt-0.5">
                    {{ $enrollmentStats['selesai'] }} selesai · {{ $enrollmentStats['aktif'] }} aktif dari {{ $enrollmentStats['total'] }} kursus
                </p>
            </div>
            <div class="w-full sm:w-48">
                <div class="h-3 bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full bg-[#cfd8bd] rounded-full transition-all"
                         style="width: {{ $enrollmentStats['avg_progress'] }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        @foreach($child->enrollments as $enrollment)
        <div class="bg-white rounded-xl border border-[#bfb5a3]/10 shadow-sm p-5">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-[#ebe5d8] to-[#ddd4c2] rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[#8a5a31]">library_books</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <h4 class="font-headline font-bold text-[#2b2721] truncate">{{ $enrollment->course?->title }}</h4>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold flex-shrink-0
                            {{ $enrollment->status === 'completed' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-[#8a5a31]' }}">
                            {{ $enrollment->status === 'completed' ? 'Selesai' : 'Aktif' }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-xs text-[#6b6358]">
                        <span>{{ $enrollment->course?->category }}</span>
                        @php
                            $levelLabels = ['beginner' => 'Pemula', 'intermediate' => 'Menengah', 'advanced' => 'Mahir'];
                        @endphp
                        <span>{{ $levelLabels[$enrollment->course?->level] ?? $enrollment->course?->level }}</span>
                        <span>Mulai: {{ $enrollment->enrolled_at?->format('d M Y') }}</span>
                    </div>
                    <div class="mt-3 flex items-center gap-3">
                        <div class="flex-1 h-2 bg-[#ebe5d8] rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all
                                {{ $enrollment->progress_percentage >= 100 ? 'bg-green-500' : 'bg-[#8a5a31]' }}"
                                 style="width: {{ $enrollment->progress_percentage }}%"></div>
                        </div>
                        <span class="text-sm font-bold text-[#8a5a31] flex-shrink-0">{{ $enrollment->progress_percentage }}%</span>
                    </div>
                    @if($enrollment->status === 'completed' && $enrollment->completed_at)
                    <p class="text-xs text-green-600 mt-1.5 font-semibold">
                        ✓ Selesai pada {{ $enrollment->completed_at->format('d M Y') }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
    @endif

    {{-- ═══════════ TAHFIDZ ═══════════ --}}
    @if($activeTab === 'tahfidz')

    <div class="flex items-center justify-between mb-2">
        <h3 class="text-lg font-headline font-bold text-[#2b2721]">Rekap Hafalan Al-Qur'an</h3>
        <div class="flex items-center gap-2 text-xs font-semibold text-[#6b6358] bg-[#ebe5d8] px-3 py-1.5 rounded-full">
            <span class="material-symbols-outlined text-sm">visibility</span>
            Hanya Lihat
        </div>
    </div>

    <!-- Tahfidz KPIs -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-4 rounded-xl border border-[#bfb5a3]/10 shadow-sm text-center">
            <p class="text-2xl font-headline font-extrabold text-[#56663f]">{{ $tahfidzStats['total_setoran'] }}</p>
            <p class="text-xs text-[#6b6358] mt-1">Total Setoran</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-[#bfb5a3]/10 shadow-sm text-center">
            <p class="text-2xl font-headline font-extrabold text-[#8a5a31]">{{ $tahfidzStats['ziyadah'] }}</p>
            <p class="text-xs text-[#6b6358] mt-1">Ziyadah</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-[#bfb5a3]/10 shadow-sm text-center">
            <p class="text-2xl font-headline font-extrabold text-indigo-600">{{ $tahfidzStats['murojaah'] }}</p>
            <p class="text-xs text-[#6b6358] mt-1">Murojaah</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-[#bfb5a3]/10 shadow-sm text-center">
            <p class="text-2xl font-headline font-extrabold text-amber-600">{{ $tahfidzStats['avg_score'] }}</p>
            <p class="text-xs text-[#6b6358] mt-1">Rata-rata Nilai</p>
        </div>
    </div>

    <!-- Score breakdown -->
    @if($this->recentTahfidz->isNotEmpty())
    @php
        $records = $this->recentTahfidz;
        $avgKelancaran = round($records->avg('score_kelancaran'));
        $avgTajwid     = round($records->avg('score_tajwid'));
        $avgMakhorijul = round($records->avg('score_makhorijul_huruf'));
    @endphp
    <div class="bg-white rounded-xl border border-[#bfb5a3]/10 shadow-sm p-5 md:p-6">
        <h4 class="font-headline font-bold text-[#2b2721] mb-5">Rata-rata Penilaian</h4>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-sm font-semibold mb-1.5">
                    <span class="text-[#6b6358]">Kelancaran</span>
                    <span class="text-[#8a5a31]">{{ $avgKelancaran }}/100</span>
                </div>
                <div class="h-3 bg-[#ebe5d8] rounded-full overflow-hidden">
                    <div class="h-full bg-[#8a5a31] rounded-full transition-all" style="width: {{ $avgKelancaran }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm font-semibold mb-1.5">
                    <span class="text-[#6b6358]">Tajwid</span>
                    <span class="text-[#8a5a31]">{{ $avgTajwid }}/100</span>
                </div>
                <div class="h-3 bg-[#ebe5d8] rounded-full overflow-hidden">
                    <div class="h-full bg-[#8a5a31] rounded-full transition-all" style="width: {{ $avgTajwid }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm font-semibold mb-1.5">
                    <span class="text-[#6b6358]">Makhorijul Huruf</span>
                    <span class="text-[#8a5a31]">{{ $avgMakhorijul }}/100</span>
                </div>
                <div class="h-3 bg-[#ebe5d8] rounded-full overflow-hidden">
                    <div class="h-full bg-[#8a5a31] rounded-full transition-all" style="width: {{ $avgMakhorijul }}%"></div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Full Riwayat Setoran (read-only table) -->
    <div>
        <h4 class="font-headline font-bold text-[#2b2721] mb-4">Buku Mutaba'ah Digital</h4>
        <div class="bg-white rounded-xl border border-[#bfb5a3]/10 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
            <table class="w-full min-w-[540px]">
                <thead class="bg-[#f3efe6]">
                    <tr>
                        <th class="text-left text-xs font-bold uppercase tracking-wider text-[#6b6358] px-4 py-3">Tanggal</th>
                        <th class="text-left text-xs font-bold uppercase tracking-wider text-[#6b6358] px-4 py-3">Surah & Ayat</th>
                        <th class="text-left text-xs font-bold uppercase tracking-wider text-[#6b6358] px-4 py-3">Jenis</th>
                        <th class="text-center text-xs font-bold uppercase tracking-wider text-[#6b6358] px-4 py-3">Nilai</th>
                        <th class="text-left text-xs font-bold uppercase tracking-wider text-[#6b6358] px-4 py-3">Guru</th>
                        <th class="text-left text-xs font-bold uppercase tracking-wider text-[#6b6358] px-4 py-3">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f3efe6]">
                    @forelse($child->tahfidzRecords as $record)
                    <tr class="hover:bg-[#f3efe6] transition-colors">
                        <td class="px-4 py-3 text-sm text-[#6b6358] whitespace-nowrap">
                            {{ $record->tanggal_setoran?->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-semibold text-[#2b2721]">{{ $record->surah?->nama_latin }}</p>
                            <p class="text-xs text-[#6b6358]">Ayat {{ $record->ayat_mulai }}-{{ $record->ayat_selesai }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $record->jenis_badge_color }}">
                                {{ $record->jenis_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm font-bold text-[#8a5a31]">{{ $record->score_rata_rata }}</span>
                            <span class="text-xs font-bold ml-1
                                {{ $record->grade === 'A' ? 'text-[#56663f]' : ($record->grade === 'B' ? 'text-green-600' : ($record->grade === 'C' ? 'text-amber-600' : 'text-[#a3402c]')) }}">
                                ({{ $record->grade }})
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-[#6b6358]">{{ $record->instruktur?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-[#6b6358] max-w-[180px] truncate">
                            {{ $record->keterangan ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-[#6b6358] text-sm">Belum ada catatan setoran hafalan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    @endif

    {{-- ═══════════ PROFIL ═══════════ --}}
    @if($activeTab === 'profil')

    <div class="flex items-center justify-between mb-2">
        <h3 class="text-lg font-headline font-bold text-[#2b2721]">Data Profil Anak</h3>
        <div class="flex items-center gap-2 text-xs font-semibold text-[#6b6358] bg-[#ebe5d8] px-3 py-1.5 rounded-full">
            <span class="material-symbols-outlined text-sm">lock</span>
            Hanya Baca
        </div>
    </div>

    <div class="bg-white rounded-xl border border-[#bfb5a3]/10 shadow-sm overflow-hidden">
        <!-- Avatar Header -->
        <div class="bg-gradient-to-r from-[#ebe5d8] to-[#ddd4c2] p-6 flex items-center gap-5">
            <img src="{{ $child->avatar_url }}" class="w-20 h-20 rounded-2xl object-cover border-4 border-white shadow-md">
            <div>
                <h4 class="font-headline font-bold text-xl text-[#2b2721]">{{ $child->name }}</h4>
                <p class="text-sm text-[#6b6358]">{{ $child->email }}</p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-2 py-0.5 bg-[#8a5a31] text-white rounded-full text-xs font-bold">Siswa</span>
                    @if($child->class_group)
                    <span class="px-2 py-0.5 bg-[#ebe5d8] text-[#6b6358] rounded-full text-xs font-bold">{{ $child->class_group }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profile Fields -->
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            @php
                $fields = [
                    ['label' => 'NIS', 'value' => $child->nis ?? '-', 'icon' => 'badge'],
                    ['label' => 'NISN', 'value' => $child->nisn ?? '-', 'icon' => 'numbers'],
                    ['label' => 'Jenis Kelamin', 'value' => ['L' => 'Laki-laki', 'P' => 'Perempuan', 'male' => 'Laki-laki', 'female' => 'Perempuan'][$child->gender] ?? '-', 'icon' => 'person'],
                    ['label' => 'Tanggal Lahir', 'value' => $child->birth_date?->format('d M Y') ?? '-', 'icon' => 'cake'],
                    ['label' => 'No. HP / WA', 'value' => $child->phone ?? '-', 'icon' => 'phone'],
                    ['label' => 'Alamat', 'value' => $child->address ?? '-', 'icon' => 'home'],
                    ['label' => 'Nama Wali', 'value' => $child->guardian_name ?? '-', 'icon' => 'family_restroom'],
                    ['label' => 'Kelas', 'value' => $child->class_group ?? '-', 'icon' => 'school'],
                    ['label' => 'XP Points', 'value' => number_format($child->xp_points) . ' XP', 'icon' => 'bolt'],
                    ['label' => 'Streak Belajar', 'value' => $child->streak_days . ' hari', 'icon' => 'local_fire_department'],
                ];
            @endphp
            @foreach($fields as $field)
            <div class="flex items-start gap-3 p-3 bg-[#f3efe6] rounded-xl">
                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-[#8a5a31] text-sm">{{ $field['icon'] }}</span>
                </div>
                <div>
                    <p class="text-xs text-[#6b6358] font-medium">{{ $field['label'] }}</p>
                    <p class="text-sm font-semibold text-[#2b2721] mt-0.5">{{ $field['value'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @endif

    @endif {{-- end selectedChild --}}
    @endif {{-- end children --}}

</div>
