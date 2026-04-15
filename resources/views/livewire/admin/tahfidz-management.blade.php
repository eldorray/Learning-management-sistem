<div class="p-4 md:p-8 max-w-7xl mx-auto space-y-6 md:space-y-8">

    <!-- Header -->
    <section class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs block mb-2">Admin Panel</span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-headline font-extrabold tracking-tight text-[#2c2f31]">Manajemen Tahfidz</h2>
            <p class="text-[#595c5e] mt-1 text-sm md:text-base">Monitor, atur target, dan kelola halaqoh hafalan Al-Qur'an.</p>
        </div>
    </section>

    @if(session('success'))
    <div class="bg-[#73f2dd]/30 border border-[#00675c]/20 text-[#00675c] px-4 py-3 rounded-xl flex items-center gap-3">
        <span class="material-symbols-outlined">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    <!-- Tabs -->
    <div class="flex gap-1 bg-[#eef1f3] p-1 rounded-xl w-full md:w-auto overflow-x-auto">
        @foreach(['dashboard' => 'Dashboard', 'targets' => 'Target Kelas', 'groups' => 'Halaqoh', 'records' => 'Semua Setoran'] as $tab => $label)
        <button wire:click="$set('activeTab', '{{ $tab }}')"
                class="flex-1 md:flex-none px-4 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap
                    {{ $activeTab === $tab ? 'bg-white text-[#0058ba] shadow-sm' : 'text-[#595c5e] hover:text-[#2c2f31]' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ═══════════ DASHBOARD ═══════════ --}}
    @if($activeTab === 'dashboard')

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
            <div class="w-10 h-10 bg-[#73f2dd]/30 rounded-xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-[#00675c] text-sm">auto_stories</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-[#00675c]">{{ number_format($this->totalSetoran) }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Total Setoran</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-[#0058ba] text-sm">group</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-[#0058ba]">{{ $this->totalSiswaAktif }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Siswa Aktif</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-amber-600 text-sm">groups</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-amber-600">{{ $this->totalHalaqoh }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Halaqoh Aktif</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-green-600 text-sm">grade</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-green-600">{{ $this->avgScore }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Rata-rata Nilai</p>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Top Students -->
        <div class="lg:col-span-5">
            <h3 class="text-lg font-headline font-bold text-[#2c2f31] mb-4">Siswa Terbaik (Setoran)</h3>
            <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
                @if($this->topStudents->isEmpty())
                <div class="p-6 text-center text-[#595c5e] text-sm">Belum ada data setoran.</div>
                @else
                <div class="divide-y divide-[#f5f7f9]">
                    @foreach($this->topStudents as $index => $student)
                    <div class="flex items-center gap-3 p-4">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold
                            {{ $index === 0 ? 'bg-amber-100 text-amber-700' : ($index === 1 ? 'bg-slate-100 text-slate-600' : ($index === 2 ? 'bg-orange-100 text-orange-700' : 'bg-[#eef1f3] text-[#595c5e]')) }}">
                            {{ $index + 1 }}
                        </div>
                        <img src="{{ $student->avatar_url }}" class="w-9 h-9 rounded-full object-cover">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-[#2c2f31] truncate">{{ $student->name }}</p>
                            <p class="text-xs text-[#595c5e]">{{ $student->total_setoran }} setoran</p>
                        </div>
                        <span class="text-xs font-bold text-[#00675c] bg-[#73f2dd]/30 px-2 py-1 rounded-lg">
                            {{ round($student->avg_score ?? 0) }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Recent Records -->
        <div class="lg:col-span-7">
            <h3 class="text-lg font-headline font-bold text-[#2c2f31] mb-4">Setoran Terbaru</h3>
            <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                <table class="w-full min-w-[480px]">
                    <thead class="bg-[#f5f7f9]">
                        <tr>
                            <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Siswa</th>
                            <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Surah</th>
                            <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Jenis</th>
                            <th class="text-right text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f5f7f9]">
                        @forelse($this->recentRecords as $record)
                        <tr class="hover:bg-[#f5f7f9] transition-colors">
                            <td class="px-4 py-3">
                                <p class="text-sm font-semibold text-[#2c2f31] truncate max-w-[120px]">{{ $record->student?->name }}</p>
                                <p class="text-xs text-[#595c5e]">{{ $record->tanggal_setoran?->format('d M') }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-[#2c2f31]">{{ $record->surah?->nama_latin }}</p>
                                <p class="text-xs text-[#595c5e]">{{ $record->ayat_mulai }}-{{ $record->ayat_selesai }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $record->jenis_badge_color }}">
                                    {{ $record->jenis_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-sm font-bold text-[#0058ba]">{{ $record->score_rata_rata }}</span>
                                <span class="text-xs text-[#595c5e]">/100</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-8 text-center text-[#595c5e] text-sm">Belum ada setoran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

    @endif

    {{-- ═══════════ TARGETS ═══════════ --}}
    @if($activeTab === 'targets')

    <div class="flex items-center justify-between">
        <h3 class="text-lg font-headline font-bold text-[#2c2f31]">Target Hafalan per Kelas</h3>
        <button wire:click="openTargetModal()"
                class="flex items-center gap-2 px-4 py-2 bg-[#0058ba] text-white rounded-full text-sm font-bold hover:bg-[#004da4] transition-colors">
            <span class="material-symbols-outlined text-sm">add</span>
            Tambah Target
        </button>
    </div>

    <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full min-w-[500px]">
            <thead class="bg-[#f5f7f9]">
                <tr>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-6 py-3">Kelas</th>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-6 py-3">Semester</th>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-6 py-3">Target Hafalan</th>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-6 py-3">Keterangan</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f5f7f9]">
                @forelse($targets as $target)
                <tr class="hover:bg-[#f5f7f9] transition-colors">
                    <td class="px-6 py-4 font-semibold text-sm text-[#2c2f31]">{{ $target->tingkat_kelas }}</td>
                    <td class="px-6 py-4 text-sm text-[#595c5e]">Semester {{ $target->semester }}</td>
                    <td class="px-6 py-4 text-sm text-[#2c2f31]">{{ $target->label }}</td>
                    <td class="px-6 py-4 text-sm text-[#595c5e] max-w-xs truncate">{{ $target->keterangan ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 justify-end">
                            <button wire:click="openTargetModal({{ $target->id }})"
                                    class="p-1.5 text-[#595c5e] hover:text-[#0058ba] transition-colors">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button wire:click="deleteTarget({{ $target->id }})"
                                    wire:confirm="Yakin hapus target ini?"
                                    class="p-1.5 text-[#595c5e] hover:text-[#b31b25] transition-colors">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-10 text-center text-[#595c5e] text-sm">Belum ada target. Tambahkan target hafalan.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    @endif

    {{-- ═══════════ GROUPS / HALAQOH ═══════════ --}}
    @if($activeTab === 'groups')

    <div class="flex items-center justify-between">
        <h3 class="text-lg font-headline font-bold text-[#2c2f31]">Halaqoh Tahfidz</h3>
        <button wire:click="openGroupModal()"
                class="flex items-center gap-2 px-4 py-2 bg-[#0058ba] text-white rounded-full text-sm font-bold hover:bg-[#004da4] transition-colors">
            <span class="material-symbols-outlined text-sm">add</span>
            Buat Halaqoh
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($groups as $group)
        <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-5 border-b border-[#eef1f3]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h4 class="font-headline font-bold text-[#2c2f31]">{{ $group->nama_halaqoh }}</h4>
                        <p class="text-xs text-[#595c5e] mt-0.5">{{ $group->tingkat_kelas ?? 'Semua Kelas' }}</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $group->is_active ? 'bg-[#73f2dd]/30 text-[#00675c]' : 'bg-[#eef1f3] text-[#595c5e]' }}">
                        {{ $group->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <div class="flex items-center gap-2 mt-3">
                    <img src="{{ $group->instruktur?->avatar_url }}" class="w-6 h-6 rounded-full object-cover">
                    <p class="text-xs text-[#595c5e]">{{ $group->instruktur?->name }}</p>
                </div>
            </div>
            <div class="p-4">
                <div class="flex items-center justify-between text-sm mb-4">
                    <div class="flex items-center gap-1.5 text-[#595c5e]">
                        <span class="material-symbols-outlined text-sm">group</span>
                        <span>{{ $group->students_count }} siswa</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button wire:click="openPlottingModal({{ $group->id }})"
                            class="flex-1 py-2 bg-[#eef1f3] text-[#0058ba] rounded-lg text-xs font-bold hover:bg-[#dfe3e6] transition-colors">
                        Plotting Siswa
                    </button>
                    <button wire:click="openGroupModal({{ $group->id }})"
                            class="p-2 text-[#595c5e] hover:text-[#0058ba] transition-colors">
                        <span class="material-symbols-outlined text-sm">edit</span>
                    </button>
                    <button wire:click="deleteGroup({{ $group->id }})"
                            wire:confirm="Yakin hapus halaqoh ini?"
                            class="p-2 text-[#595c5e] hover:text-[#b31b25] transition-colors">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-[#595c5e]">
            <span class="material-symbols-outlined text-4xl mb-2 block">groups</span>
            Belum ada halaqoh. Buat halaqoh pertama.
        </div>
        @endforelse
    </div>

    @endif

    {{-- ═══════════ ALL RECORDS ═══════════ --}}
    @if($activeTab === 'records')

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#595c5e] text-sm pointer-events-none">search</span>
            <input type="text" wire:model.live.debounce.300ms="searchRecord"
                   placeholder="Cari nama siswa..."
                   class="w-full pl-9 pr-4 py-2.5 bg-white border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
        </div>
        <select wire:model.live="filterSurah"
                class="px-4 py-2.5 bg-white border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
            <option value="">Semua Surah</option>
            @foreach($surahs as $surah)
            <option value="{{ $surah->id }}">{{ $surah->nama_latin }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterJenis"
                class="px-4 py-2.5 bg-white border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
            <option value="">Semua Jenis</option>
            <option value="ziyadah">Ziyadah</option>
            <option value="murojaah">Murojaah</option>
        </select>
    </div>

    <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full min-w-[640px]">
            <thead class="bg-[#f5f7f9]">
                <tr>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Siswa</th>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Surah & Ayat</th>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Jenis</th>
                    <th class="text-center text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Kelancaran</th>
                    <th class="text-center text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Tajwid</th>
                    <th class="text-center text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Makhorijul</th>
                    <th class="text-center text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Rata-rata</th>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f5f7f9]">
                @forelse($records as $record)
                <tr class="hover:bg-[#f5f7f9] transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <img src="{{ $record->student?->avatar_url }}" class="w-7 h-7 rounded-full object-cover flex-shrink-0">
                            <p class="text-sm font-semibold text-[#2c2f31] truncate">{{ $record->student?->name }}</p>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-sm text-[#2c2f31]">{{ $record->surah?->nama_latin }}</p>
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
                    <td class="px-4 py-3 text-sm text-[#595c5e]">{{ $record->tanggal_setoran?->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="py-10 text-center text-[#595c5e] text-sm">Belum ada catatan setoran.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($records->hasPages())
        <div class="px-4 py-3 border-t border-[#eef1f3]">{{ $records->links() }}</div>
        @endif
    </div>

    @endif

    {{-- ═══════════ MODALS ═══════════ --}}

    {{-- Target Modal --}}
    @if($showTargetModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl w-full sm:max-w-lg max-h-[92vh] overflow-y-auto">
            <div class="w-10 h-1 bg-[#abadaf]/30 rounded-full mx-auto mt-3 sm:hidden"></div>
            <div class="p-6">
                <h3 class="font-headline font-bold text-lg text-[#2c2f31] mb-6">
                    {{ $editTargetId ? 'Edit Target Hafalan' : 'Tambah Target Hafalan' }}
                </h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Tingkat Kelas</label>
                            <input type="text" wire:model="targetKelas" placeholder="cth: Kelas 7"
                                   class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                            @error('targetKelas')<p class="text-[#b31b25] text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Semester</label>
                            <select wire:model="targetSemester"
                                    class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                                <option value="1">Semester 1</option>
                                <option value="2">Semester 2</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Surah Mulai</label>
                            <select wire:model="targetSurahMulaiId"
                                    class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                                <option value="">Pilih Surah</option>
                                @foreach($surahs as $surah)
                                <option value="{{ $surah->id }}">{{ $surah->nomor }}. {{ $surah->nama_latin }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Ayat Mulai</label>
                            <input type="number" wire:model="targetAyatMulai" min="1"
                                   class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Surah Selesai</label>
                            <select wire:model="targetSurahSelesaiId"
                                    class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                                <option value="">Pilih Surah</option>
                                @foreach($surahs as $surah)
                                <option value="{{ $surah->id }}">{{ $surah->nomor }}. {{ $surah->nama_latin }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Ayat Selesai</label>
                            <input type="number" wire:model="targetAyatSelesai" min="1"
                                   class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Keterangan</label>
                        <textarea wire:model="targetKeterangan" rows="2" placeholder="Opsional..."
                                  class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 resize-none"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button wire:click="$set('showTargetModal', false)"
                            class="flex-1 py-3 border border-[#abadaf]/20 text-[#595c5e] rounded-xl text-sm font-semibold hover:bg-[#f5f7f9] transition-colors">
                        Batal
                    </button>
                    <button wire:click="saveTarget"
                            class="flex-1 py-3 bg-[#0058ba] text-white rounded-xl text-sm font-bold hover:bg-[#004da4] transition-colors">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Group Modal --}}
    @if($showGroupModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl w-full sm:max-w-lg max-h-[92vh] overflow-y-auto">
            <div class="w-10 h-1 bg-[#abadaf]/30 rounded-full mx-auto mt-3 sm:hidden"></div>
            <div class="p-6">
                <h3 class="font-headline font-bold text-lg text-[#2c2f31] mb-6">
                    {{ $editGroupId ? 'Edit Halaqoh' : 'Buat Halaqoh Baru' }}
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Nama Halaqoh</label>
                        <input type="text" wire:model="groupNama" placeholder="cth: Halaqoh Al-Fatih"
                               class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                        @error('groupNama')<p class="text-[#b31b25] text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Guru Tahfidz</label>
                        <select wire:model="groupInstrukturId"
                                class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                            <option value="">Pilih Instruktur</option>
                            @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                            @endforeach
                        </select>
                        @error('groupInstrukturId')<p class="text-[#b31b25] text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Tingkat Kelas (Opsional)</label>
                        <input type="text" wire:model="groupKelas" placeholder="cth: Kelas 7A"
                               class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Deskripsi</label>
                        <textarea wire:model="groupDeskripsi" rows="2" placeholder="Opsional..."
                                  class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 resize-none"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button wire:click="$set('showGroupModal', false)"
                            class="flex-1 py-3 border border-[#abadaf]/20 text-[#595c5e] rounded-xl text-sm font-semibold hover:bg-[#f5f7f9] transition-colors">
                        Batal
                    </button>
                    <button wire:click="saveGroup"
                            class="flex-1 py-3 bg-[#0058ba] text-white rounded-xl text-sm font-bold hover:bg-[#004da4] transition-colors">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Plotting Modal --}}
    @if($showPlottingModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl w-full sm:max-w-lg max-h-[92vh] overflow-y-auto">
            <div class="w-10 h-1 bg-[#abadaf]/30 rounded-full mx-auto mt-3 sm:hidden"></div>
            <div class="p-6">
                <h3 class="font-headline font-bold text-lg text-[#2c2f31] mb-2">Plotting Siswa ke Halaqoh</h3>
                <p class="text-sm text-[#595c5e] mb-5">Pilih siswa yang akan ditempatkan di halaqoh ini.</p>
                <div class="space-y-2 max-h-72 overflow-y-auto">
                    @foreach($allStudents as $student)
                    <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-[#f5f7f9] cursor-pointer transition-colors">
                        <input type="checkbox" wire:model="selectedStudents" value="{{ $student->id }}"
                               class="rounded border-[#abadaf]/30 text-[#0058ba] focus:ring-[#0058ba]/20">
                        <img src="{{ $student->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
                        <div>
                            <p class="text-sm font-semibold text-[#2c2f31]">{{ $student->name }}</p>
                            <p class="text-xs text-[#595c5e]">{{ $student->email }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                <div class="flex gap-3 mt-6">
                    <button wire:click="$set('showPlottingModal', false)"
                            class="flex-1 py-3 border border-[#abadaf]/20 text-[#595c5e] rounded-xl text-sm font-semibold hover:bg-[#f5f7f9] transition-colors">
                        Batal
                    </button>
                    <button wire:click="savePlotting"
                            class="flex-1 py-3 bg-[#0058ba] text-white rounded-xl text-sm font-bold hover:bg-[#004da4] transition-colors">
                        Simpan Plotting
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
