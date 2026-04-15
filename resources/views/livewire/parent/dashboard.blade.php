<div class="p-4 md:p-8 max-w-7xl mx-auto space-y-6 md:space-y-8">

    <!-- Header -->
    <section>
        <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs block mb-2">Portal Orang Tua</span>
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-headline font-extrabold tracking-tight text-[#2c2f31]">
            Pantau Perkembangan Anak
        </h2>
        <p class="text-[#595c5e] mt-1 text-sm md:text-base">Lihat hasil belajar dan hafalan anak Anda secara real-time.</p>
    </section>

    {{-- No children linked --}}
    @if($this->children->isEmpty())
    <div class="py-20 text-center bg-white rounded-2xl border border-[#abadaf]/10 shadow-sm">
        <span class="material-symbols-outlined text-6xl text-[#abadaf] block mb-4">family_restroom</span>
        <h3 class="font-headline font-bold text-xl text-[#2c2f31] mb-2">Belum Ada Anak Terdaftar</h3>
        <p class="text-[#595c5e] text-sm max-w-sm mx-auto">Hubungi admin sekolah untuk menghubungkan akun Anda dengan data siswa.</p>
    </div>
    @else

    <!-- Child Selector -->
    @if($this->children->count() > 1)
    <div class="flex gap-3 overflow-x-auto pb-1">
        @foreach($this->children as $child)
        <button wire:click="selectChild({{ $child->id }})"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl border-2 flex-shrink-0 transition-all
                    {{ $selectedChildId === $child->id
                        ? 'bg-[#0058ba] border-[#0058ba] text-white shadow-lg shadow-blue-500/20'
                        : 'bg-white border-[#abadaf]/20 text-[#2c2f31] hover:border-[#0058ba]/30' }}">
            <img src="{{ $child->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
            <div class="text-left">
                <p class="text-sm font-bold truncate max-w-[120px]">{{ $child->name }}</p>
                <p class="text-xs {{ $selectedChildId === $child->id ? 'text-[#6c9fff]' : 'text-[#595c5e]' }}">
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
    <div class="relative overflow-hidden bg-gradient-to-br from-[#0058ba] to-[#004da4] rounded-2xl p-5 md:p-6 text-white">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative z-10 flex items-center gap-4 md:gap-6">
            <img src="{{ $child->avatar_url }}" class="w-16 h-16 md:w-20 md:h-20 rounded-2xl object-cover border-2 border-white/30 flex-shrink-0">
            <div class="flex-1 min-w-0">
                <p class="text-sm text-[#6c9fff] font-semibold uppercase tracking-widest">Profil Anak</p>
                <h3 class="text-xl md:text-2xl font-headline font-extrabold mt-0.5 truncate">{{ $child->name }}</h3>
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2 text-sm text-[#6c9fff]">
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
    <div class="flex gap-1 bg-[#eef1f3] p-1 rounded-xl overflow-x-auto">
        @foreach(['overview' => 'Ringkasan', 'kursus' => 'Kursus', 'tahfidz' => 'Tahfidz', 'profil' => 'Profil'] as $tab => $label)
        <button wire:click="$set('activeTab', '{{ $tab }}')"
                class="flex-1 px-4 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap
                    {{ $activeTab === $tab ? 'bg-white text-[#0058ba] shadow-sm' : 'text-[#595c5e] hover:text-[#2c2f31]' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ═══════════ OVERVIEW ═══════════ --}}
    @if($activeTab === 'overview')

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
        <!-- Kursus Progress -->
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-[#0058ba] text-sm">library_books</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-[#0058ba]">{{ $enrollmentStats['total'] }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Kursus Diikuti</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm">
            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-green-600 text-sm">task_alt</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-green-600">{{ $enrollmentStats['avg_progress'] }}%</p>
            <p class="text-xs text-[#595c5e] mt-1">Rata-rata Progres</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm">
            <div class="w-10 h-10 bg-[#73f2dd]/30 rounded-xl flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-[#00675c] text-sm">auto_stories</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-[#00675c]">{{ $tahfidzStats['total_setoran'] }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Total Setoran</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-amber-600 text-sm">local_fire_department</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-amber-600">{{ $tahfidzStats['streak'] }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Hari Berturut</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kursus Terkini -->
        <div>
            <h3 class="text-lg font-headline font-bold text-[#2c2f31] mb-4">Progres Kursus</h3>
            <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
                @if($child->enrollments->isEmpty())
                <div class="p-8 text-center text-[#595c5e] text-sm">Belum ada kursus yang diikuti.</div>
                @else
                <div class="divide-y divide-[#f5f7f9]">
                    @foreach($child->enrollments->take(5) as $enrollment)
                    <div class="p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-[#eef1f3] rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-[#0058ba] text-sm">library_books</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-[#2c2f31] truncate">{{ $enrollment->course?->title }}</p>
                                <div class="flex items-center justify-between mt-1.5 gap-2">
                                    <div class="flex-1 h-1.5 bg-[#eef1f3] rounded-full overflow-hidden">
                                        <div class="h-full bg-[#0058ba] rounded-full transition-all"
                                             style="width: {{ $enrollment->progress_percentage }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-[#0058ba] flex-shrink-0">{{ $enrollment->progress_percentage }}%</span>
                                </div>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-xs text-[#595c5e]">{{ $enrollment->course?->category }}</span>
                                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold
                                        {{ $enrollment->status === 'completed' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-[#0058ba]' }}">
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
            <h3 class="text-lg font-headline font-bold text-[#2c2f31] mb-4">Setoran Tahfidz Terkini</h3>
            <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
                @if($this->recentTahfidz->isEmpty())
                <div class="p-8 text-center text-[#595c5e] text-sm">Belum ada catatan setoran.</div>
                @else
                <div class="divide-y divide-[#f5f7f9]">
                    @foreach($this->recentTahfidz as $record)
                    <div class="flex items-center gap-3 p-4">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
                            {{ $record->jenis_setoran === 'ziyadah' ? 'bg-[#73f2dd]/30' : 'bg-[#6c9fff]/20' }}">
                            <span class="material-symbols-outlined text-sm
                                {{ $record->jenis_setoran === 'ziyadah' ? 'text-[#00675c]' : 'text-[#0058ba]' }}">
                                auto_stories
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-[#2c2f31] truncate">
                                {{ $record->surah?->nama_latin }} ({{ $record->ayat_mulai }}-{{ $record->ayat_selesai }})
                            </p>
                            <p class="text-xs text-[#595c5e]">
                                {{ $record->jenis_label }} · {{ $record->tanggal_setoran?->format('d M Y') }}
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-[#0058ba]">{{ $record->score_rata_rata }}</p>
                            <p class="text-xs font-bold
                                {{ $record->grade === 'A' ? 'text-[#00675c]' : ($record->grade === 'B' ? 'text-green-600' : 'text-amber-600') }}">
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
        <h3 class="text-lg font-headline font-bold text-[#2c2f31] mb-4">Tren Setoran & Nilai (6 Bulan)</h3>
        <div class="bg-white p-6 rounded-xl border border-[#abadaf]/10 shadow-sm">
            @php $maxTotal = $this->tahfidzMonthlyTrend->max('total') ?: 1; @endphp
            <div class="flex items-end gap-3 h-40 justify-around">
                @foreach($this->tahfidzMonthlyTrend as $item)
                <div class="flex flex-col items-center gap-1.5 flex-1">
                    <span class="text-xs font-bold text-[#00675c]">{{ $item->avg_score }}</span>
                    <div class="w-full relative rounded-t-lg overflow-hidden"
                         style="height: {{ max(8, round(($item->total / $maxTotal) * 120)) }}px">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0058ba] to-[#6c9fff] rounded-t-lg"></div>
                    </div>
                    <span class="text-[10px] text-[#595c5e] text-center leading-tight">{{ $item->label }}</span>
                    <span class="text-[10px] font-bold text-[#595c5e]">{{ $item->total }}x</span>
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
        <h3 class="text-lg font-headline font-bold text-[#2c2f31]">Kursus yang Diikuti</h3>
        <div class="flex items-center gap-2 text-xs font-semibold text-[#595c5e] bg-[#eef1f3] px-3 py-1.5 rounded-full">
            <span class="material-symbols-outlined text-sm">visibility</span>
            Hanya Lihat
        </div>
    </div>

    @if($child->enrollments->isEmpty())
    <div class="py-16 text-center bg-white rounded-2xl border border-[#abadaf]/10 shadow-sm">
        <span class="material-symbols-outlined text-5xl text-[#abadaf] block mb-3">library_books</span>
        <p class="text-[#595c5e] text-sm">Anak Anda belum mengikuti kursus apapun.</p>
    </div>
    @else

    <!-- Overall Progress -->
    <div class="bg-gradient-to-br from-[#0058ba] to-[#004da4] rounded-2xl p-5 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-sm text-[#6c9fff] font-semibold">Progres Keseluruhan</p>
                <p class="text-3xl font-headline font-extrabold mt-1">{{ $enrollmentStats['avg_progress'] }}%</p>
                <p class="text-sm text-[#6c9fff] mt-0.5">
                    {{ $enrollmentStats['selesai'] }} selesai · {{ $enrollmentStats['aktif'] }} aktif dari {{ $enrollmentStats['total'] }} kursus
                </p>
            </div>
            <div class="w-full sm:w-48">
                <div class="h-3 bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full bg-[#73f2dd] rounded-full transition-all"
                         style="width: {{ $enrollmentStats['avg_progress'] }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        @foreach($child->enrollments as $enrollment)
        <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm p-5">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-[#eef1f3] to-[#dfe3e6] rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[#0058ba]">library_books</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <h4 class="font-headline font-bold text-[#2c2f31] truncate">{{ $enrollment->course?->title }}</h4>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold flex-shrink-0
                            {{ $enrollment->status === 'completed' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-[#0058ba]' }}">
                            {{ $enrollment->status === 'completed' ? 'Selesai' : 'Aktif' }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-xs text-[#595c5e]">
                        <span>{{ $enrollment->course?->category }}</span>
                        @php
                            $levelLabels = ['beginner' => 'Pemula', 'intermediate' => 'Menengah', 'advanced' => 'Mahir'];
                        @endphp
                        <span>{{ $levelLabels[$enrollment->course?->level] ?? $enrollment->course?->level }}</span>
                        <span>Mulai: {{ $enrollment->enrolled_at?->format('d M Y') }}</span>
                    </div>
                    <div class="mt-3 flex items-center gap-3">
                        <div class="flex-1 h-2 bg-[#eef1f3] rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all
                                {{ $enrollment->progress_percentage >= 100 ? 'bg-green-500' : 'bg-[#0058ba]' }}"
                                 style="width: {{ $enrollment->progress_percentage }}%"></div>
                        </div>
                        <span class="text-sm font-bold text-[#0058ba] flex-shrink-0">{{ $enrollment->progress_percentage }}%</span>
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
        <h3 class="text-lg font-headline font-bold text-[#2c2f31]">Rekap Hafalan Al-Qur'an</h3>
        <div class="flex items-center gap-2 text-xs font-semibold text-[#595c5e] bg-[#eef1f3] px-3 py-1.5 rounded-full">
            <span class="material-symbols-outlined text-sm">visibility</span>
            Hanya Lihat
        </div>
    </div>

    <!-- Tahfidz KPIs -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-4 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
            <p class="text-2xl font-headline font-extrabold text-[#00675c]">{{ $tahfidzStats['total_setoran'] }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Total Setoran</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
            <p class="text-2xl font-headline font-extrabold text-[#0058ba]">{{ $tahfidzStats['ziyadah'] }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Ziyadah</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
            <p class="text-2xl font-headline font-extrabold text-indigo-600">{{ $tahfidzStats['murojaah'] }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Murojaah</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
            <p class="text-2xl font-headline font-extrabold text-amber-600">{{ $tahfidzStats['avg_score'] }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Rata-rata Nilai</p>
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
    <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm p-5 md:p-6">
        <h4 class="font-headline font-bold text-[#2c2f31] mb-5">Rata-rata Penilaian</h4>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-sm font-semibold mb-1.5">
                    <span class="text-[#595c5e]">Kelancaran</span>
                    <span class="text-[#0058ba]">{{ $avgKelancaran }}/100</span>
                </div>
                <div class="h-3 bg-[#eef1f3] rounded-full overflow-hidden">
                    <div class="h-full bg-[#0058ba] rounded-full transition-all" style="width: {{ $avgKelancaran }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm font-semibold mb-1.5">
                    <span class="text-[#595c5e]">Tajwid</span>
                    <span class="text-[#0058ba]">{{ $avgTajwid }}/100</span>
                </div>
                <div class="h-3 bg-[#eef1f3] rounded-full overflow-hidden">
                    <div class="h-full bg-[#0058ba] rounded-full transition-all" style="width: {{ $avgTajwid }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm font-semibold mb-1.5">
                    <span class="text-[#595c5e]">Makhorijul Huruf</span>
                    <span class="text-[#0058ba]">{{ $avgMakhorijul }}/100</span>
                </div>
                <div class="h-3 bg-[#eef1f3] rounded-full overflow-hidden">
                    <div class="h-full bg-[#0058ba] rounded-full transition-all" style="width: {{ $avgMakhorijul }}%"></div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Full Riwayat Setoran (read-only table) -->
    <div>
        <h4 class="font-headline font-bold text-[#2c2f31] mb-4">Buku Mutaba'ah Digital</h4>
        <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
            <table class="w-full min-w-[540px]">
                <thead class="bg-[#f5f7f9]">
                    <tr>
                        <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Tanggal</th>
                        <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Surah & Ayat</th>
                        <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Jenis</th>
                        <th class="text-center text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Nilai</th>
                        <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Guru</th>
                        <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f5f7f9]">
                    @forelse($child->tahfidzRecords as $record)
                    <tr class="hover:bg-[#f5f7f9] transition-colors">
                        <td class="px-4 py-3 text-sm text-[#595c5e] whitespace-nowrap">
                            {{ $record->tanggal_setoran?->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-semibold text-[#2c2f31]">{{ $record->surah?->nama_latin }}</p>
                            <p class="text-xs text-[#595c5e]">Ayat {{ $record->ayat_mulai }}-{{ $record->ayat_selesai }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $record->jenis_badge_color }}">
                                {{ $record->jenis_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm font-bold text-[#0058ba]">{{ $record->score_rata_rata }}</span>
                            <span class="text-xs font-bold ml-1
                                {{ $record->grade === 'A' ? 'text-[#00675c]' : ($record->grade === 'B' ? 'text-green-600' : ($record->grade === 'C' ? 'text-amber-600' : 'text-[#b31b25]')) }}">
                                ({{ $record->grade }})
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-[#595c5e]">{{ $record->instruktur?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-[#595c5e] max-w-[180px] truncate">
                            {{ $record->keterangan ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-[#595c5e] text-sm">Belum ada catatan setoran hafalan.</td>
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
        <h3 class="text-lg font-headline font-bold text-[#2c2f31]">Data Profil Anak</h3>
        <div class="flex items-center gap-2 text-xs font-semibold text-[#595c5e] bg-[#eef1f3] px-3 py-1.5 rounded-full">
            <span class="material-symbols-outlined text-sm">lock</span>
            Hanya Baca
        </div>
    </div>

    <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
        <!-- Avatar Header -->
        <div class="bg-gradient-to-r from-[#eef1f3] to-[#dfe3e6] p-6 flex items-center gap-5">
            <img src="{{ $child->avatar_url }}" class="w-20 h-20 rounded-2xl object-cover border-4 border-white shadow-md">
            <div>
                <h4 class="font-headline font-bold text-xl text-[#2c2f31]">{{ $child->name }}</h4>
                <p class="text-sm text-[#595c5e]">{{ $child->email }}</p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-2 py-0.5 bg-[#0058ba] text-white rounded-full text-xs font-bold">Siswa</span>
                    @if($child->class_group)
                    <span class="px-2 py-0.5 bg-[#eef1f3] text-[#595c5e] rounded-full text-xs font-bold">{{ $child->class_group }}</span>
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
                    ['label' => 'Jenis Kelamin', 'value' => $child->gender === 'male' ? 'Laki-laki' : ($child->gender === 'female' ? 'Perempuan' : '-'), 'icon' => 'person'],
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
            <div class="flex items-start gap-3 p-3 bg-[#f5f7f9] rounded-xl">
                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-[#0058ba] text-sm">{{ $field['icon'] }}</span>
                </div>
                <div>
                    <p class="text-xs text-[#595c5e] font-medium">{{ $field['label'] }}</p>
                    <p class="text-sm font-semibold text-[#2c2f31] mt-0.5">{{ $field['value'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @endif

    @endif {{-- end selectedChild --}}
    @endif {{-- end children --}}

</div>
