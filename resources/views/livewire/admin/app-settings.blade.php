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

</div>
