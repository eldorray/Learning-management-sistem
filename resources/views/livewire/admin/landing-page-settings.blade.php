<div class="p-4 md:p-8 max-w-4xl mx-auto space-y-6 md:space-y-8">
    <header class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-headline font-extrabold tracking-tight text-[#2c2f31]">Pengaturan Landing Page</h2>
            <p class="text-[#595c5e] mt-2 text-sm">Ubah teks, tautan, dan gambar tanpa mengubah desain halaman. Teks disimpan sebagai teks biasa, bukan HTML.</p>
        </div>
        <a href="/landing-preview" target="_blank" rel="noopener noreferrer" class="px-5 py-3 rounded-full border border-[#0058ba]/20 text-[#0058ba] text-sm font-bold hover:bg-blue-50">Pratinjau halaman</a>
    </header>

    @if (session('success'))
        <div role="status" class="bg-[#73f2dd]/30 border border-[#00675c]/20 text-[#00675c] px-4 py-3 rounded-xl">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div role="alert" class="bg-red-50 border border-red-200 text-[#b31b25] px-4 py-3 rounded-xl">
            <p class="font-semibold">Pengaturan belum disimpan. Periksa isian berikut:</p>
            <ul class="list-disc pl-5 mt-2 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="sticky top-16 z-20 flex justify-end py-3 bg-[#f5f7f9]">
            <button type="submit" wire:loading.attr="disabled" class="px-6 py-3 bg-[#0058ba] text-white text-sm font-bold rounded-full disabled:opacity-50">Simpan semua perubahan</button>
        </div>
        @foreach ($sections as $section => $fields)
            <details class="bg-white rounded-2xl border border-[#abadaf]/20 shadow-sm overflow-hidden" wire:key="section-{{ $loop->index }}" @if($loop->first || $section === 'Latar gedung sekolah') open @endif>
                <summary class="px-6 py-5 border-b border-[#eef1f3] font-headline font-bold text-lg text-[#2c2f31] cursor-pointer">{{ $section }}</summary>
                <div class="p-6 space-y-6">
                    @if ($section === 'Latar gedung sekolah')
                        <p class="text-sm text-[#595c5e]">Unggah foto gedung, pilih “Foto gedung sekolah”, lalu simpan. Foto menggantikan seluruh dunia 3D beserta dekorasi kuil; teks dan navigasi tetap tersedia. Gunakan foto horizontal beresolusi tinggi. Gambar galeri tetap dikelola pada bagian masing-masing.</p>
                    @endif
                    @foreach ($fields as $key => $field)
                        <div wire:key="landing-field-{{ $key }}">
                            <label for="landing-{{ $key }}" class="block text-sm font-semibold text-[#2c2f31] mb-2">{{ $field['label'] }}</label>
                            @if ($field['type'] === 'image')
                                <div class="space-y-3">
                                    @if (isset($previews[$key]))
                                        <img src="{{ $previews[$key] }}" alt="Pratinjau unggahan" class="w-full max-w-sm h-40 object-contain rounded-xl bg-[#f5f7f9] border border-[#abadaf]/20">
                                    @elseif ($values[$key] !== '')
                                        <img src="{{ $values[$key] }}" alt="{{ $field['label'] }} — gambar tersimpan" loading="lazy" class="w-full max-w-sm h-40 object-contain rounded-xl bg-[#f5f7f9] border border-[#abadaf]/20">
                                    @else
                                        <p class="text-sm text-[#595c5e]">Ilustrasi bawaan digunakan. Lihat pada pratinjau halaman.</p>
                                    @endif
                                    <input id="landing-{{ $key }}" type="file" wire:model="uploads.{{ $key }}" accept="image/png,image/jpeg,image/webp" aria-describedby="hint-{{ $key }} error-{{ $key }}" class="block w-full text-sm text-[#595c5e] file:mr-3 file:px-4 file:py-2 file:rounded-full file:border-0 file:bg-blue-50 file:text-[#0058ba]">
                                    <p id="hint-{{ $key }}" class="text-xs text-[#595c5e]">PNG, JPG, atau WebP · Maksimal 4 MB. Pilihan gambar diterapkan saat semua perubahan disimpan.</p>
                                    <p wire:loading wire:target="uploads.{{ $key }}" class="text-sm text-[#0058ba]" role="status">Mengunggah gambar…</p>
                                    <button type="button" wire:click="resetImage('{{ $key }}')" wire:confirm="Kembalikan gambar ini ke bawaan? Perubahan langsung disimpan." wire:loading.attr="disabled" class="text-sm font-semibold text-[#b31b25] hover:underline disabled:opacity-50">Kembalikan gambar bawaan</button>
                                    @error('uploads.'.$key)<p id="error-{{ $key }}" class="text-[#b31b25] text-xs">{{ $message }}</p>@enderror
                                </div>
                            @else
                                @if ($field['type'] === 'select')
                                    <select id="landing-{{ $key }}" wire:model="texts.{{ $key }}" class="w-full px-4 py-3 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm">
                                        @foreach ($field['options'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                @elseif ($field['type'] === 'link')
                                    <input id="landing-{{ $key }}" type="text" wire:model="texts.{{ $key }}" maxlength="2000" aria-describedby="error-{{ $key }}" class="w-full px-4 py-3 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 focus:border-[#0058ba]">
                                    <p class="text-xs text-[#595c5e] mt-1">Gunakan #top, #hero, #gate, #pathways, #lessons, atau #eternity. @unless(in_array($key, ['link_02', 'link_03', 'link_04', 'link_05'])) Tautan ini juga mendukung /login dan /register. @endunless</p>
                                @else
                                    <textarea id="landing-{{ $key }}" wire:model="texts.{{ $key }}" rows="2" maxlength="{{ $key === 'js_wordmark' ? 12 : 2000 }}" aria-describedby="error-{{ $key }}" class="w-full px-4 py-3 bg-[#f5f7f9] border border-[#abadaf]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 focus:border-[#0058ba]"></textarea>
                                @endif
                                @error('texts.'.$key)<p id="error-{{ $key }}" class="text-[#b31b25] text-xs mt-1">{{ $message }}</p>@enderror
                            @endif
                        </div>
                    @endforeach
                </div>
            </details>
        @endforeach
        <div class="flex flex-wrap items-center justify-end gap-3">
            <span wire:loading wire:target="save" role="status" class="text-sm text-[#595c5e]">Menyimpan…</span>
            <button type="submit" wire:loading.attr="disabled" class="px-8 py-3.5 bg-[#0058ba] text-white font-bold rounded-full hover:bg-[#004da4] disabled:opacity-50">Simpan semua perubahan</button>
        </div>
    </form>
</div>
