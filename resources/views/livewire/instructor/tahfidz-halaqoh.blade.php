<div class="p-4 md:p-8 max-w-7xl mx-auto space-y-6 md:space-y-8">

    <!-- Header -->
    <section>
        <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs block mb-2">Guru Tahfidz</span>
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-headline font-extrabold tracking-tight text-[#2c2f31]">Halaqoh Saya</h2>
        <p class="text-[#595c5e] mt-1 text-sm md:text-base">Kelola setoran dan pantau perkembangan siswa.</p>
    </section>

    @if(session('success'))
    <div class="bg-[#73f2dd]/30 border border-[#00675c]/20 text-[#00675c] px-4 py-3 rounded-xl flex items-center gap-3">
        <span class="material-symbols-outlined">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    <!-- Tabs -->
    <div class="flex gap-1 bg-[#eef1f3] p-1 rounded-xl overflow-x-auto">
        @foreach(['halaqoh' => 'Daftar Halaqoh', 'riwayat' => 'Riwayat Setoran'] as $tab => $label)
        <button wire:click="$set('activeTab', '{{ $tab }}')"
                class="flex-1 px-4 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap
                    {{ $activeTab === $tab ? 'bg-white text-[#0058ba] shadow-sm' : 'text-[#595c5e] hover:text-[#2c2f31]' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ═══════════ HALAQOH ═══════════ --}}
    @if($activeTab === 'halaqoh')

    @if($this->myGroups->isEmpty())
    <div class="py-16 text-center">
        <span class="material-symbols-outlined text-5xl text-[#abadaf] block mb-3">groups</span>
        <p class="text-[#595c5e]">Anda belum memiliki halaqoh. Hubungi admin untuk penetapan halaqoh.</p>
    </div>
    @else

    <!-- Group Selector (if multiple) -->
    @if($this->myGroups->count() > 1)
    <div class="flex gap-2 overflow-x-auto pb-1">
        @foreach($this->myGroups as $group)
        <button wire:click="$set('selectedGroup', {{ $group->id }})"
                class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-semibold border transition-all
                    {{ $selectedGroup === $group->id ? 'bg-[#0058ba] text-white border-[#0058ba]' : 'bg-white text-[#595c5e] border-[#abadaf]/20 hover:border-[#0058ba]/30' }}">
            {{ $group->nama_halaqoh }}
        </button>
        @endforeach
    </div>
    @endif

    @if($this->selectedGroupData)
    <!-- Group Info Banner -->
    <div class="relative overflow-hidden bg-gradient-to-br from-[#0058ba] to-[#004da4] rounded-2xl p-6 text-white">
        <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-sm text-[#6c9fff] font-semibold uppercase tracking-widest">Halaqoh Aktif</p>
                <h3 class="text-2xl font-headline font-extrabold mt-1">{{ $this->selectedGroupData->nama_halaqoh }}</h3>
                <p class="text-[#6c9fff] text-sm mt-1">{{ $this->selectedGroupData->tingkat_kelas ?? 'Semua Kelas' }} · {{ $this->selectedGroupData->students_count }} siswa</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-white/10 backdrop-blur-sm px-4 py-3 rounded-xl text-center">
                    <p class="text-2xl font-headline font-extrabold">{{ $this->selectedGroupData->students->sum('total_setoran') }}</p>
                    <p class="text-xs text-[#6c9fff]">Total Setoran</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="space-y-3">
        @forelse($this->selectedGroupData->students as $student)
        <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
            <div class="p-4">
                <div class="flex items-center gap-3">
                    <img src="{{ $student->avatar_url }}" class="w-11 h-11 rounded-xl object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-[#2c2f31] truncate">{{ $student->name }}</p>
                        <p class="text-xs text-[#595c5e]">{{ $student->total_setoran ?? 0 }} setoran · Nilai rata-rata {{ round($student->avg_score ?? 0) }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button wire:click="$set('viewStudentId', {{ $student->id }})"
                                class="p-2 text-[#595c5e] hover:text-[#0058ba] transition-colors">
                            <span class="material-symbols-outlined text-sm">visibility</span>
                        </button>
                        <button wire:click="openSetoranModal({{ $student->id }})"
                                class="flex items-center gap-1.5 px-3 py-2 bg-[#0058ba] text-white rounded-lg text-xs font-bold hover:bg-[#004da4] transition-colors">
                            <span class="material-symbols-outlined text-sm">add</span>
                            Setoran
                        </button>
                    </div>
                </div>

                <!-- Mini progress bar -->
                @php
                    $pct = min(100, (int) round((($student->total_setoran ?? 0) / 30) * 100));
                @endphp
                <div class="mt-3 flex items-center gap-2">
                    <div class="flex-1 h-1.5 bg-[#eef1f3] rounded-full overflow-hidden">
                        <div class="h-full bg-[#00675c] rounded-full transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="text-xs text-[#595c5e] font-medium flex-shrink-0">{{ $pct }}%</span>
                </div>
            </div>

            {{-- Student detail (expandable) --}}
            @if($viewStudentId === $student->id && $this->studentDetail)
            <div class="border-t border-[#eef1f3] p-4 bg-[#f5f7f9]">
                <h4 class="text-sm font-bold text-[#2c2f31] mb-3">Riwayat Setoran Terkini</h4>
                <div class="space-y-2">
                    @forelse($this->studentDetail->tahfidzRecords as $rec)
                    <div class="bg-white rounded-lg p-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg {{ $rec->jenis_setoran === 'ziyadah' ? 'bg-[#73f2dd]/30' : 'bg-[#6c9fff]/20' }} flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-sm {{ $rec->jenis_setoran === 'ziyadah' ? 'text-[#00675c]' : 'text-[#0058ba]' }}">auto_stories</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-[#2c2f31]">{{ $rec->surah?->nama_latin }} ({{ $rec->ayat_mulai }}-{{ $rec->ayat_selesai }})</p>
                            <p class="text-xs text-[#595c5e]">{{ $rec->jenis_label }} · {{ $rec->tanggal_setoran?->format('d M Y') }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="text-sm font-bold text-[#0058ba]">{{ $rec->score_rata_rata }}</span>
                            <span class="text-xs font-bold text-[#595c5e]">/100</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-[#595c5e] text-center py-2">Belum ada setoran.</p>
                    @endforelse
                </div>
                <button wire:click="$set('viewStudentId', null)"
                        class="mt-3 text-xs text-[#595c5e] hover:text-[#2c2f31] transition-colors">
                    Tutup
                </button>
            </div>
            @endif
        </div>
        @empty
        <div class="py-8 text-center text-[#595c5e] text-sm">Belum ada siswa di halaqoh ini.</div>
        @endforelse
    </div>
    @endif

    @endif
    @endif

    {{-- ═══════════ RIWAYAT SETORAN ═══════════ --}}
    @if($activeTab === 'riwayat')

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#595c5e] text-sm pointer-events-none">search</span>
            <input type="text" wire:model.live.debounce.300ms="searchRiwayat"
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
        <table class="w-full min-w-[580px]">
            <thead class="bg-[#f5f7f9]">
                <tr>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Siswa</th>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Surah & Ayat</th>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Jenis</th>
                    <th class="text-center text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Nilai</th>
                    <th class="text-left text-xs font-bold uppercase tracking-wider text-[#595c5e] px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f5f7f9]">
                @forelse($riwayat as $record)
                <tr class="hover:bg-[#f5f7f9] transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <img src="{{ $record->student?->avatar_url }}" class="w-7 h-7 rounded-full object-cover flex-shrink-0">
                            <p class="text-sm font-semibold text-[#2c2f31] truncate max-w-[100px]">{{ $record->student?->name }}</p>
                        </div>
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
                    <td class="px-4 py-3 text-center">
                        <span class="text-sm font-bold text-[#0058ba]">{{ $record->score_rata_rata }}</span>
                        <span class="text-xs font-bold ml-1 text-[#595c5e]">({{ $record->grade }})</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-[#595c5e]">{{ $record->tanggal_setoran?->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <button wire:click="deleteRecord({{ $record->id }})"
                                wire:confirm="Yakin hapus setoran ini?"
                                class="p-1.5 text-[#595c5e] hover:text-[#b31b25] transition-colors">
                            <span class="material-symbols-outlined text-sm">delete</span>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-10 text-center text-[#595c5e] text-sm">Belum ada catatan setoran.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($riwayat->hasPages())
        <div class="px-4 py-3 border-t border-[#eef1f3]">{{ $riwayat->links() }}</div>
        @endif
    </div>

    @endif

    {{-- Setoran Modal --}}
    @if($showSetoranModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl w-full sm:max-w-lg max-h-[95vh] overflow-y-auto">
            <div class="w-10 h-1 bg-[#abadaf]/30 rounded-full mx-auto mt-3 sm:hidden"></div>
            <div class="p-6">
                <h3 class="font-headline font-bold text-lg text-[#2c2f31] mb-1">Catat Setoran</h3>
                <p class="text-sm text-[#595c5e] mb-6">Input hasil setoran hafalan siswa.</p>

                <div class="space-y-4">
                    <!-- Surah & Ayat -->
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Surah</label>
                        <select wire:model.live="setoranSurahId"
                                class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                            <option value="">Pilih Surah</option>
                            @foreach($surahs as $surah)
                            <option value="{{ $surah->id }}">{{ $surah->nomor }}. {{ $surah->nama_latin }} ({{ $surah->jumlah_ayat }} ayat)</option>
                            @endforeach
                        </select>
                        @error('setoranSurahId')<p class="text-[#b31b25] text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Ayat Mulai</label>
                            <input type="number" wire:model="setoranAyatMulai" min="1"
                                   class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Ayat Selesai</label>
                            <input type="number" wire:model="setoranAyatSelesai" min="1"
                                   class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                            @error('setoranAyatSelesai')<p class="text-[#b31b25] text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <!-- Jenis & Tanggal -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Jenis Setoran</label>
                            <select wire:model="setoranJenis"
                                    class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                                <option value="ziyadah">Ziyadah (Hafalan Baru)</option>
                                <option value="murojaah">Murojaah (Ulangan)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Tanggal</label>
                            <input type="date" wire:model="setoranTanggal"
                                   class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                        </div>
                    </div>

                    <!-- Scores -->
                    <div class="bg-[#f5f7f9] rounded-xl p-4 space-y-3">
                        <p class="text-sm font-semibold text-[#2c2f31]">Penilaian (0-100)</p>

                        <div>
                            <div class="flex justify-between text-xs font-medium text-[#595c5e] mb-1">
                                <span>Kelancaran</span>
                                <span class="font-bold text-[#0058ba]">{{ $setoranKelancaran }}</span>
                            </div>
                            <input type="range" wire:model.live="setoranKelancaran" min="0" max="100"
                                   class="w-full accent-[#0058ba]">
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-medium text-[#595c5e] mb-1">
                                <span>Tajwid</span>
                                <span class="font-bold text-[#0058ba]">{{ $setoranTajwid }}</span>
                            </div>
                            <input type="range" wire:model.live="setoranTajwid" min="0" max="100"
                                   class="w-full accent-[#0058ba]">
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-medium text-[#595c5e] mb-1">
                                <span>Makhorijul Huruf</span>
                                <span class="font-bold text-[#0058ba]">{{ $setoranMakhorijul }}</span>
                            </div>
                            <input type="range" wire:model.live="setoranMakhorijul" min="0" max="100"
                                   class="w-full accent-[#0058ba]">
                        </div>

                        <div class="pt-2 border-t border-[#abadaf]/20 flex items-center justify-between">
                            <span class="text-xs font-semibold text-[#595c5e]">Rata-rata</span>
                            @php $avg = round(($setoranKelancaran + $setoranTajwid + $setoranMakhorijul) / 3); @endphp
                            <span class="text-lg font-headline font-extrabold text-[#0058ba]">{{ $avg }}/100</span>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Catatan / Evaluasi</label>
                        <textarea wire:model="setoranKeterangan" rows="2"
                                  placeholder="Opsional: catatan untuk siswa..."
                                  class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 resize-none"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button wire:click="$set('showSetoranModal', false)"
                            class="flex-1 py-3 border border-[#abadaf]/20 text-[#595c5e] rounded-xl text-sm font-semibold hover:bg-[#f5f7f9] transition-colors">
                        Batal
                    </button>
                    <button wire:click="saveSetoran"
                            class="flex-1 py-3.5 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-white rounded-xl text-sm font-bold hover:scale-[1.01] transition-transform shadow-lg shadow-blue-500/20">
                        Simpan Setoran
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
