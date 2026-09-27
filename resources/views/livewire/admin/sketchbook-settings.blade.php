<div class="p-4 md:p-8 max-w-4xl mx-auto space-y-6 md:space-y-8">
    <header class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-headline font-extrabold tracking-tight text-[#2b2721]">Pengaturan Landing Page</h2>
            <p class="text-[#6b6358] mt-2 text-sm">Ubah teks, tautan, dan ilustrasi buku sketsa tanpa mengubah desain halaman. Teks disimpan sebagai teks biasa, bukan HTML.</p>
        </div>
        <a href="/landing-preview" target="_blank" rel="noopener noreferrer" class="px-5 py-3 rounded-full border border-[#8a5a31]/20 text-[#8a5a31] text-sm font-bold hover:bg-blue-50">Pratinjau halaman</a>
    </header>

    @if (session('success'))
        <div role="status" class="bg-[#cfd8bd]/30 border border-[#56663f]/20 text-[#56663f] px-4 py-3 rounded-xl">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div role="alert" class="bg-red-50 border border-red-200 text-[#a3402c] px-4 py-3 rounded-xl">
            <p class="font-semibold">Pengaturan belum disimpan. Periksa isian berikut:</p>
            <ul class="list-disc pl-5 mt-2 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="sticky top-16 z-20 flex justify-end py-3 bg-[#f3efe6]">
            <button type="submit" wire:loading.attr="disabled" class="px-6 py-3 bg-[#8a5a31] text-white text-sm font-bold rounded-full disabled:opacity-50">Simpan semua perubahan</button>
        </div>
        @foreach ($sections as $section => $fields)
            <details class="bg-white rounded-2xl border border-[#bfb5a3]/20 shadow-sm overflow-hidden" wire:key="section-{{ $loop->index }}" @if($loop->first) open @endif>
                <summary class="px-6 py-5 border-b border-[#ebe5d8] font-headline font-bold text-lg text-[#2b2721] cursor-pointer">{{ $section }}</summary>
                <div class="p-6 space-y-6">
                    @if ($section === 'Halaman buku')
                        <p class="text-sm text-[#6b6358]">Setiap halaman adalah satu bentang buku terbuka. Foto biasa (sebaiknya horizontal) otomatis ditempatkan di atas kertas buku: dipotong pas ke area halaman, tepinya dilebur ke kertas, dan lipatan tengah diberi bayangan. Bagian atas dan bawah foto yang sangat tinggi akan terpotong. PNG transparan 1760×1240 berupa buku terbuka utuh dipakai apa adanya. Judul tampil di bawah buku dan pada daftar halaman.</p>
                    @endif
                    @foreach ($fields as $key => $field)
                        <div wire:key="landing-field-{{ $key }}">
                            <label for="landing-{{ $key }}" class="block text-sm font-semibold text-[#2b2721] mb-2">{{ $field['label'] }}</label>
                            @if ($field['type'] === 'image')
                                <div class="space-y-3">
                                    <img src="{{ $previews[$key] ?? ($values[$key] ?: '/landing-pages/meng-to-sketchbook/'.$field['file']) }}" alt="{{ $field['label'] }}{{ isset($previews[$key]) ? ' — pratinjau unggahan' : ($values[$key] ? ' — tersimpan' : ' — bawaan') }}" loading="lazy" class="w-full max-w-sm h-40 object-contain rounded-xl bg-[#f3efe6] border border-[#bfb5a3]/20">
                                    <input id="landing-{{ $key }}" type="file" wire:model="uploads.{{ $key }}" accept="image/png,image/jpeg,image/webp" aria-describedby="hint-{{ $key }} error-{{ $key }}" class="block w-full text-sm text-[#6b6358] file:mr-3 file:px-4 file:py-2 file:rounded-full file:border-0 file:bg-blue-50 file:text-[#8a5a31]">
                                    <p id="hint-{{ $key }}" class="text-xs text-[#6b6358]">PNG, JPG, atau WebP · Maksimal 4 MB. Diterapkan saat semua perubahan disimpan.</p>
                                    <p wire:loading wire:target="uploads.{{ $key }}" class="text-sm text-[#8a5a31]" role="status">Mengunggah gambar…</p>
                                    @if ($values[$key] !== '')
                                        <button type="button" wire:click="resetImage('{{ $key }}')" wire:confirm="Kembalikan ilustrasi ini ke bawaan? Perubahan langsung disimpan." wire:loading.attr="disabled" class="text-sm font-semibold text-[#a3402c] hover:underline disabled:opacity-50">Kembalikan ilustrasi bawaan</button>
                                    @endif
                                    @error('uploads.'.$key)<p id="error-{{ $key }}" class="text-[#a3402c] text-xs">{{ $message }}</p>@enderror
                                </div>
                            @else
                                @if ($field['type'] === 'text')
                                    <textarea id="landing-{{ $key }}" wire:model="texts.{{ $key }}" rows="{{ str_starts_with($key, 'bio_') && ! str_contains($key, 'link') ? 4 : 1 }}" maxlength="2000" aria-describedby="error-{{ $key }}" class="w-full px-4 py-3 bg-[#f3efe6] border border-[#bfb5a3]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#8a5a31]/20 focus:border-[#8a5a31]"></textarea>
                                @else
                                    <input id="landing-{{ $key }}" type="text" wire:model="texts.{{ $key }}" maxlength="500" aria-describedby="hint-{{ $key }} error-{{ $key }}" class="w-full px-4 py-3 bg-[#f3efe6] border border-[#bfb5a3]/20 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#8a5a31]/20 focus:border-[#8a5a31]">
                                    <p id="hint-{{ $key }}" class="text-xs text-[#6b6358] mt-1">
                                        @if ($field['type'] === 'social')
                                            Alamat lengkap diawali https://
                                        @else
                                            Gunakan /login, /register, #sketchbook, #about, #plates, #contact, alamat https://, atau mailto:.
                                        @endif
                                    </p>
                                @endif
                                @error('texts.'.$key)<p id="error-{{ $key }}" class="text-[#a3402c] text-xs mt-1">{{ $message }}</p>@enderror
                            @endif
                        </div>
                    @endforeach
                </div>
            </details>
        @endforeach
        <div class="flex flex-wrap items-center justify-end gap-3">
            <span wire:loading wire:target="save" role="status" class="text-sm text-[#6b6358]">Menyimpan…</span>
            <button type="submit" wire:loading.attr="disabled" class="px-8 py-3.5 bg-[#8a5a31] text-white font-bold rounded-full hover:bg-[#6f4826] disabled:opacity-50">Simpan semua perubahan</button>
        </div>
    </form>
</div>
