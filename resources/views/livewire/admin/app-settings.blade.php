<div class="p-4 md:p-8 max-w-4xl mx-auto space-y-6 md:space-y-8">

    <!-- Header -->
    <section>
        <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs block mb-2">Admin Panel</span>
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-headline font-extrabold tracking-tight text-[#2c2f31]">Pengaturan Aplikasi</h2>
        <p class="text-[#595c5e] mt-1 text-sm md:text-base">Kelola identitas dan tampilan platform.</p>
    </section>

    <!-- Flash Message -->
    @if(session('success'))
    <div class="bg-[#73f2dd]/30 border border-[#00675c]/20 text-[#00675c] px-4 py-3 rounded-xl flex items-center gap-3 animate-fade-in">
        <span class="material-symbols-outlined">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    <form wire:submit="save" class="space-y-8">

        <!-- App Identity -->
        <div class="bg-white rounded-2xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-[#eef1f3]">
                <h3 class="font-headline font-bold text-lg text-[#2c2f31] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#0058ba]">badge</span>
                    Identitas Aplikasi
                </h3>
                <p class="text-sm text-[#595c5e] mt-1">Nama dan tagline akan muncul di seluruh halaman.</p>
            </div>
            <div class="p-6 space-y-5">
                <!-- App Name -->
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-2">Nama Aplikasi</label>
                    <input type="text" wire:model="appName"
                           class="w-full px-4 py-3 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 focus:border-[#0058ba]"
                           placeholder="Contoh: LMS Arrahmah">
                    @error('appName')
                    <p class="text-[#b31b25] text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tagline -->
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-2">Tagline / Subtitle</label>
                    <input type="text" wire:model="appTagline"
                           class="w-full px-4 py-3 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 focus:border-[#0058ba]"
                           placeholder="Contoh: Platform Pembelajaran Digital">
                    @error('appTagline')
                    <p class="text-[#b31b25] text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Logo -->
        <div class="bg-white rounded-2xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-[#eef1f3]">
                <h3 class="font-headline font-bold text-lg text-[#2c2f31] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#0058ba]">image</span>
                    Logo Aplikasi
                </h3>
                <p class="text-sm text-[#595c5e] mt-1">Logo akan tampil di sidebar, halaman login, dan register. Rekomendasi: PNG transparan, 200x200px.</p>
            </div>
            <div class="p-6">
                <div class="flex items-start gap-6">
                    <!-- Preview -->
                    <div class="flex-shrink-0">
                        <div class="w-24 h-24 rounded-2xl overflow-hidden border-2 border-dashed border-[#abadaf]/30 flex items-center justify-center bg-[#f5f7f9]">
                            @if($appLogo)
                                <img src="{{ $appLogo->temporaryUrl() }}" class="w-full h-full object-contain p-2">
                            @elseif($currentLogo)
                                <img src="{{ asset('storage/' . $currentLogo) }}" class="w-full h-full object-contain p-2">
                            @else
                                <span class="material-symbols-outlined text-[#abadaf] text-3xl">add_photo_alternate</span>
                            @endif
                        </div>
                    </div>

                    <!-- Upload -->
                    <div class="flex-1">
                        <label class="block">
                            <input type="file" wire:model="appLogo" accept="image/*" class="hidden" id="logoUpload">
                            <div class="cursor-pointer px-5 py-3 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl hover:bg-[#eef1f3] transition-colors text-center">
                                <span class="material-symbols-outlined text-[#0058ba] mb-1" style="font-size: 20px;">upload</span>
                                <p class="text-sm font-semibold text-[#2c2f31]">Pilih Logo</p>
                                <p class="text-xs text-[#595c5e]">PNG, JPG, SVG · Maks 2MB</p>
                            </div>
                        </label>
                        @error('appLogo')
                        <p class="text-[#b31b25] text-xs mt-2">{{ $message }}</p>
                        @enderror

                        @if($currentLogo)
                        <button type="button" wire:click="removeLogo"
                                class="mt-3 text-xs text-[#b31b25] hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined" style="font-size: 14px;">delete</span>
                            Hapus Logo
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Favicon -->
        <div class="bg-white rounded-2xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-[#eef1f3]">
                <h3 class="font-headline font-bold text-lg text-[#2c2f31] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#0058ba]">star</span>
                    Favicon
                </h3>
                <p class="text-sm text-[#595c5e] mt-1">Ikon kecil yang muncul di tab browser. Rekomendasi: 32x32px atau 64x64px.</p>
            </div>
            <div class="p-6">
                <div class="flex items-start gap-6">
                    <!-- Preview -->
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 rounded-xl overflow-hidden border-2 border-dashed border-[#abadaf]/30 flex items-center justify-center bg-[#f5f7f9]">
                            @if($appFavicon)
                                <img src="{{ $appFavicon->temporaryUrl() }}" class="w-full h-full object-contain p-1">
                            @elseif($currentFavicon)
                                <img src="{{ asset('storage/' . $currentFavicon) }}" class="w-full h-full object-contain p-1">
                            @else
                                <span class="material-symbols-outlined text-[#abadaf] text-xl">star</span>
                            @endif
                        </div>
                    </div>

                    <!-- Upload -->
                    <div class="flex-1">
                        <label class="block">
                            <input type="file" wire:model="appFavicon" accept="image/*" class="hidden" id="faviconUpload">
                            <div class="cursor-pointer px-5 py-3 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl hover:bg-[#eef1f3] transition-colors text-center">
                                <span class="material-symbols-outlined text-[#0058ba] mb-1" style="font-size: 20px;">upload</span>
                                <p class="text-sm font-semibold text-[#2c2f31]">Pilih Favicon</p>
                                <p class="text-xs text-[#595c5e]">PNG, ICO · Maks 1MB</p>
                            </div>
                        </label>
                        @error('appFavicon')
                        <p class="text-[#b31b25] text-xs mt-2">{{ $message }}</p>
                        @enderror

                        @if($currentFavicon)
                        <button type="button" wire:click="removeFavicon"
                                class="mt-3 text-xs text-[#b31b25] hover:underline flex items-center gap-1">
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
                    class="px-8 py-3.5 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-white font-bold rounded-full hover:scale-[1.02] transition-transform shadow-lg shadow-blue-500/20 flex items-center gap-2">
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
                <h3 class="text-xl font-headline font-bold text-[#2c2f31] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#0058ba]">calendar_month</span>
                    Tahun Ajaran
                </h3>
                <p class="text-sm text-[#595c5e] mt-0.5">Kelola dan atur tahun ajaran yang sedang aktif.</p>
            </div>
            <button wire:click="openTaForm()" type="button"
                    class="flex items-center gap-2 px-4 py-2.5 bg-[#0058ba] text-white text-sm font-bold rounded-full hover:bg-[#004da4] transition-colors">
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
        <div class="bg-white rounded-2xl border border-[#0058ba]/20 shadow-sm p-6 space-y-4">
            <h4 class="font-headline font-bold text-[#2c2f31]">
                {{ $editTaId ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran Baru' }}
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Nama <span class="text-[#595c5e] font-normal">(mis. 2025/2026)</span></label>
                    <input type="text" wire:model="taNama" placeholder="2025/2026"
                           class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 focus:border-[#0058ba]">
                    @error('taNama')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Semester</label>
                    <select wire:model="taSemester"
                            class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 focus:border-[#0058ba]">
                        <option value="1">Semester 1 (Ganjil)</option>
                        <option value="2">Semester 2 (Genap)</option>
                    </select>
                    @error('taSemester')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Tanggal Mulai</label>
                    <input type="date" wire:model="taMulai"
                           class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 focus:border-[#0058ba]">
                    @error('taMulai')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Tanggal Selesai</label>
                    <input type="date" wire:model="taSelesai"
                           class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 focus:border-[#0058ba]">
                    @error('taSelesai')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Keterangan <span class="text-[#595c5e] font-normal">(opsional)</span></label>
                    <input type="text" wire:model="taKeterangan" placeholder="Misal: Tahun ajaran berjalan"
                           class="w-full px-4 py-2.5 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 focus:border-[#0058ba]">
                </div>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button wire:click="saveTa" type="button"
                        class="px-6 py-2.5 bg-[#0058ba] text-white text-sm font-bold rounded-full hover:bg-[#004da4] transition-colors flex items-center gap-2">
                    <span wire:loading.remove wire:target="saveTa" class="material-symbols-outlined text-sm">save</span>
                    <span wire:loading wire:target="saveTa" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    Simpan
                </button>
                <button wire:click="$set('showTaForm', false)" type="button"
                        class="px-6 py-2.5 bg-[#eef1f3] text-[#595c5e] text-sm font-semibold rounded-full hover:bg-[#abadaf]/20 transition-colors">
                    Batal
                </button>
            </div>
        </div>
        @endif

        <!-- Tabel tahun ajaran -->
        <div class="bg-white rounded-2xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#f5f7f9] border-b border-[#eef1f3]">
                        <th class="text-left px-5 py-3.5 font-semibold text-[#595c5e] text-xs uppercase tracking-wider">Tahun Ajaran</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-[#595c5e] text-xs uppercase tracking-wider">Periode</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-[#595c5e] text-xs uppercase tracking-wider">Keterangan</th>
                        <th class="text-center px-5 py-3.5 font-semibold text-[#595c5e] text-xs uppercase tracking-wider">Status</th>
                        <th class="text-right px-5 py-3.5 font-semibold text-[#595c5e] text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eef1f3]">
                    @forelse($tahunAjaranList as $ta)
                    <tr class="hover:bg-[#f5f7f9]/50 transition-colors {{ $ta->is_aktif ? 'bg-blue-50/40' : '' }}">
                        <td class="px-5 py-4">
                            <div class="font-bold text-[#2c2f31]">{{ $ta->nama }}</div>
                            <div class="text-xs text-[#595c5e]">Semester {{ $ta->semester }}</div>
                        </td>
                        <td class="px-5 py-4 text-[#595c5e] text-xs">
                            {{ $ta->tanggal_mulai->format('d M Y') }} —
                            {{ $ta->tanggal_selesai->format('d M Y') }}
                        </td>
                        <td class="px-5 py-4 text-[#595c5e] text-xs">
                            {{ $ta->keterangan ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($ta->is_aktif)
                                <span class="inline-flex items-center gap-1 bg-[#73f2dd]/30 text-[#00675c] text-xs font-bold px-3 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-[#00675c] rounded-full animate-pulse"></span>
                                    Aktif
                                </span>
                            @else
                                <button wire:click="setAktif({{ $ta->id }})" type="button"
                                        class="inline-flex items-center gap-1 bg-[#eef1f3] text-[#595c5e] hover:bg-[#0058ba] hover:text-white text-xs font-semibold px-3 py-1 rounded-full transition-colors">
                                    <span class="material-symbols-outlined text-xs">radio_button_unchecked</span>
                                    Jadikan Aktif
                                </button>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="openTaForm({{ $ta->id }})" type="button"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-[#eef1f3] text-[#595c5e] hover:text-[#0058ba] transition-colors">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                </button>
                                @if(!$ta->is_aktif)
                                <button wire:click="deleteTa({{ $ta->id }})"
                                        wire:confirm="Hapus tahun ajaran {{ $ta->nama }} Semester {{ $ta->semester }}?"
                                        type="button"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 text-[#595c5e] hover:text-red-600 transition-colors">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-[#595c5e] text-sm">
                            Belum ada tahun ajaran. Klik <strong>Tambah</strong> untuk memulai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>


</div>
