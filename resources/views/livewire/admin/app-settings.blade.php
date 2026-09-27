<div class="p-4 md:p-8 max-w-4xl mx-auto space-y-6 md:space-y-8">

    <!-- Header -->
    <section>
        <span class="text-[#56663f] font-semibold uppercase tracking-widest text-xs block mb-2">Admin Panel</span>
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-headline font-extrabold tracking-tight text-[#2b2721]">Pengaturan Aplikasi</h2>
        <p class="text-[#6b6358] mt-1 text-sm md:text-base">Kelola identitas dan tampilan platform.</p>
    </section>

    <!-- Flash Message -->
    @if(session('success'))
    <div class="bg-[#cfd8bd]/30 border border-[#56663f]/20 text-[#56663f] px-4 py-3 rounded-xl flex items-center gap-3 animate-fade-in">
        <span class="material-symbols-outlined">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    <form wire:submit="save" class="space-y-8">

        <!-- App Identity -->
        <div class="bg-white rounded-2xl border border-[#bfb5a3]/10 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-[#ebe5d8]">
                <h3 class="font-headline font-bold text-lg text-[#2b2721] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#8a5a31]">badge</span>
                    Identitas Aplikasi
                </h3>
                <p class="text-sm text-[#6b6358] mt-1">Nama dan tagline akan muncul di seluruh halaman.</p>
            </div>
            <div class="p-6 space-y-5">
                <!-- App Name -->
                <div>
                    <label class="block text-sm font-semibold text-[#2b2721] mb-2">Nama Aplikasi</label>
                    <input type="text" wire:model="appName"
                           class="w-full px-4 py-3 bg-[#f3efe6] border border-[#bfb5a3]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#8a5a31]/20 focus:border-[#8a5a31]"
                           placeholder="Contoh: LMS Arrahmah">
                    @error('appName')
                    <p class="text-[#a3402c] text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tagline -->
                <div>
                    <label class="block text-sm font-semibold text-[#2b2721] mb-2">Tagline / Subtitle</label>
                    <input type="text" wire:model="appTagline"
                           class="w-full px-4 py-3 bg-[#f3efe6] border border-[#bfb5a3]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#8a5a31]/20 focus:border-[#8a5a31]"
                           placeholder="Contoh: Platform Pembelajaran Digital">
                    @error('appTagline')
                    <p class="text-[#a3402c] text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Logo -->
        <div class="bg-white rounded-2xl border border-[#bfb5a3]/10 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-[#ebe5d8]">
                <h3 class="font-headline font-bold text-lg text-[#2b2721] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#8a5a31]">image</span>
                    Logo Aplikasi
                </h3>
                <p class="text-sm text-[#6b6358] mt-1">Logo akan tampil di sidebar, halaman login, dan register. Rekomendasi: PNG transparan, 200x200px.</p>
            </div>
            <div class="p-6">
                <div class="flex items-start gap-6">
                    <!-- Preview -->
                    <div class="flex-shrink-0">
                        <div class="w-24 h-24 rounded-2xl overflow-hidden border-2 border-dashed border-[#bfb5a3]/30 flex items-center justify-center bg-[#f3efe6]">
                            @if($appLogo)
                                <img src="{{ $appLogo->temporaryUrl() }}" class="w-full h-full object-contain p-2">
                            @elseif($currentLogo)
                                <img src="{{ asset('storage/' . $currentLogo) }}" class="w-full h-full object-contain p-2">
                            @else
                                <span class="material-symbols-outlined text-[#bfb5a3] text-3xl">add_photo_alternate</span>
                            @endif
                        </div>
                    </div>

                    <!-- Upload -->
                    <div class="flex-1">
                        <label class="block">
                            <input type="file" wire:model="appLogo" accept="image/*" class="hidden" id="logoUpload">
                            <div class="cursor-pointer px-5 py-3 bg-[#f3efe6] border border-[#bfb5a3]/20 rounded-xl hover:bg-[#ebe5d8] transition-colors text-center">
                                <span class="material-symbols-outlined text-[#8a5a31] mb-1" style="font-size: 20px;">upload</span>
                                <p class="text-sm font-semibold text-[#2b2721]">Pilih Logo</p>
                                <p class="text-xs text-[#6b6358]">PNG, JPG, SVG · Maks 2MB</p>
                            </div>
                        </label>
                        @error('appLogo')
                        <p class="text-[#a3402c] text-xs mt-2">{{ $message }}</p>
                        @enderror

                        @if($currentLogo)
                        <button type="button" wire:click="removeLogo"
                                class="mt-3 text-xs text-[#a3402c] hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined" style="font-size: 14px;">delete</span>
                            Hapus Logo
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Favicon -->
        <div class="bg-white rounded-2xl border border-[#bfb5a3]/10 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-[#ebe5d8]">
                <h3 class="font-headline font-bold text-lg text-[#2b2721] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#8a5a31]">star</span>
                    Favicon
                </h3>
                <p class="text-sm text-[#6b6358] mt-1">Ikon kecil yang muncul di tab browser. Rekomendasi: 32x32px atau 64x64px.</p>
            </div>
            <div class="p-6">
                <div class="flex items-start gap-6">
                    <!-- Preview -->
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 rounded-xl overflow-hidden border-2 border-dashed border-[#bfb5a3]/30 flex items-center justify-center bg-[#f3efe6]">
                            @if($appFavicon)
                                <img src="{{ $appFavicon->temporaryUrl() }}" class="w-full h-full object-contain p-1">
                            @elseif($currentFavicon)
                                <img src="{{ asset('storage/' . $currentFavicon) }}" class="w-full h-full object-contain p-1">
                            @else
                                <span class="material-symbols-outlined text-[#bfb5a3] text-xl">star</span>
                            @endif
                        </div>
                    </div>

                    <!-- Upload -->
                    <div class="flex-1">
                        <label class="block">
                            <input type="file" wire:model="appFavicon" accept="image/*" class="hidden" id="faviconUpload">
                            <div class="cursor-pointer px-5 py-3 bg-[#f3efe6] border border-[#bfb5a3]/20 rounded-xl hover:bg-[#ebe5d8] transition-colors text-center">
                                <span class="material-symbols-outlined text-[#8a5a31] mb-1" style="font-size: 20px;">upload</span>
                                <p class="text-sm font-semibold text-[#2b2721]">Pilih Favicon</p>
                                <p class="text-xs text-[#6b6358]">PNG, ICO · Maks 1MB</p>
                            </div>
                        </label>
                        @error('appFavicon')
                        <p class="text-[#a3402c] text-xs mt-2">{{ $message }}</p>
                        @enderror

                        @if($currentFavicon)
                        <button type="button" wire:click="removeFavicon"
                                class="mt-3 text-xs text-[#a3402c] hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined" style="font-size: 14px;">delete</span>
                            Hapus Favicon
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit"
                    class="px-8 py-3.5 bg-gradient-to-br from-[#8a5a31] to-[#6f4826] text-white font-bold rounded-full hover:scale-[1.02] transition-transform shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <span wire:loading.remove wire:target="save" class="material-symbols-outlined text-sm">save</span>
                <span wire:loading wire:target="save" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                Simpan Pengaturan
            </button>
        </div>

    </form>

    <!-- ── Tahun Ajaran ─────────────────────────────────────────────── -->
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-headline font-bold text-[#2b2721] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#8a5a31]">calendar_month</span>
                    Tahun Ajaran
                </h3>
                <p class="text-sm text-[#6b6358] mt-0.5">Kelola dan atur tahun ajaran yang sedang aktif.</p>
            </div>
            <button wire:click="openTaForm()" type="button"
                    class="flex items-center gap-2 px-4 py-2.5 bg-[#8a5a31] text-white text-sm font-bold rounded-full hover:bg-[#6f4826] transition-colors">
                <span class="material-symbols-outlined text-sm">add</span>
                Tambah
            </button>
        </div>

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-sm">error</span>
            {{ session('error') }}
        </div>
        @endif

        <!-- Form tambah/edit tahun ajaran -->
        @if($showTaForm)
        <div class="bg-white rounded-2xl border border-[#8a5a31]/20 shadow-sm p-6 space-y-4">
            <h4 class="font-headline font-bold text-[#2b2721]">
                {{ $editTaId ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran Baru' }}
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-[#2b2721] mb-1.5">Nama <span class="text-[#6b6358] font-normal">(mis. 2025/2026)</span></label>
                    <input type="text" wire:model="taNama" placeholder="2025/2026"
                           class="w-full px-4 py-2.5 bg-[#f3efe6] border border-[#bfb5a3]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#8a5a31]/20 focus:border-[#8a5a31]">
                    @error('taNama')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#2b2721] mb-1.5">Semester</label>
                    <select wire:model="taSemester"
                            class="w-full px-4 py-2.5 bg-[#f3efe6] border border-[#bfb5a3]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#8a5a31]/20 focus:border-[#8a5a31]">
                        <option value="1">Semester 1 (Ganjil)</option>
                        <option value="2">Semester 2 (Genap)</option>
                    </select>
                    @error('taSemester')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#2b2721] mb-1.5">Tanggal Mulai</label>
                    <input type="date" wire:model="taMulai"
                           class="w-full px-4 py-2.5 bg-[#f3efe6] border border-[#bfb5a3]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#8a5a31]/20 focus:border-[#8a5a31]">
                    @error('taMulai')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#2b2721] mb-1.5">Tanggal Selesai</label>
                    <input type="date" wire:model="taSelesai"
                           class="w-full px-4 py-2.5 bg-[#f3efe6] border border-[#bfb5a3]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#8a5a31]/20 focus:border-[#8a5a31]">
                    @error('taSelesai')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-[#2b2721] mb-1.5">Keterangan <span class="text-[#6b6358] font-normal">(opsional)</span></label>
                    <input type="text" wire:model="taKeterangan" placeholder="Misal: Tahun ajaran berjalan"
                           class="w-full px-4 py-2.5 bg-[#f3efe6] border border-[#bfb5a3]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#8a5a31]/20 focus:border-[#8a5a31]">
                </div>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button wire:click="saveTa" type="button"
                        class="px-6 py-2.5 bg-[#8a5a31] text-white text-sm font-bold rounded-full hover:bg-[#6f4826] transition-colors flex items-center gap-2">
                    <span wire:loading.remove wire:target="saveTa" class="material-symbols-outlined text-sm">save</span>
                    <span wire:loading wire:target="saveTa" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    Simpan
                </button>
                <button wire:click="$set('showTaForm', false)" type="button"
                        class="px-6 py-2.5 bg-[#ebe5d8] text-[#6b6358] text-sm font-semibold rounded-full hover:bg-[#bfb5a3]/20 transition-colors">
                    Batal
                </button>
            </div>
        </div>
        @endif

        <!-- Tabel tahun ajaran -->
        <div class="bg-white rounded-2xl border border-[#bfb5a3]/10 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#f3efe6] border-b border-[#ebe5d8]">
                        <th class="text-left px-5 py-3.5 font-semibold text-[#6b6358] text-xs uppercase tracking-wider">Tahun Ajaran</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-[#6b6358] text-xs uppercase tracking-wider">Periode</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-[#6b6358] text-xs uppercase tracking-wider">Keterangan</th>
                        <th class="text-center px-5 py-3.5 font-semibold text-[#6b6358] text-xs uppercase tracking-wider">Status</th>
                        <th class="text-right px-5 py-3.5 font-semibold text-[#6b6358] text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ebe5d8]">
                    @forelse($tahunAjaranList as $ta)
                    <tr class="hover:bg-[#f3efe6]/50 transition-colors {{ $ta->is_aktif ? 'bg-blue-50/40' : '' }}">
                        <td class="px-5 py-4">
                            <div class="font-bold text-[#2b2721]">{{ $ta->nama }}</div>
                            <div class="text-xs text-[#6b6358]">Semester {{ $ta->semester }}</div>
                        </td>
                        <td class="px-5 py-4 text-[#6b6358] text-xs">
                            {{ $ta->tanggal_mulai->format('d M Y') }} —
                            {{ $ta->tanggal_selesai->format('d M Y') }}
                        </td>
                        <td class="px-5 py-4 text-[#6b6358] text-xs">
                            {{ $ta->keterangan ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($ta->is_aktif)
                                <span class="inline-flex items-center gap-1 bg-[#cfd8bd]/30 text-[#56663f] text-xs font-bold px-3 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-[#56663f] rounded-full animate-pulse"></span>
                                    Aktif
                                </span>
                            @else
                                <button wire:click="setAktif({{ $ta->id }})" type="button"
                                        class="inline-flex items-center gap-1 bg-[#ebe5d8] text-[#6b6358] hover:bg-[#8a5a31] hover:text-white text-xs font-semibold px-3 py-1 rounded-full transition-colors">
                                    <span class="material-symbols-outlined text-xs">radio_button_unchecked</span>
                                    Jadikan Aktif
                                </button>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="openTaForm({{ $ta->id }})" type="button"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-[#ebe5d8] text-[#6b6358] hover:text-[#8a5a31] transition-colors">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                </button>
                                @if(!$ta->is_aktif)
                                <button wire:click="deleteTa({{ $ta->id }})"
                                        wire:confirm="Hapus tahun ajaran {{ $ta->nama }} Semester {{ $ta->semester }}?"
                                        type="button"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 text-[#6b6358] hover:text-red-600 transition-colors">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-[#6b6358] text-sm">
                            Belum ada tahun ajaran. Klik <strong>Tambah</strong> untuk memulai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>


</div>
