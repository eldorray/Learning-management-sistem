<div class="bg-white rounded-2xl shadow-xl border border-[#abadaf]/10 p-6 md:p-8">
    <h2 class="text-2xl font-headline font-extrabold text-[#2c2f31] mb-2">Mulai Perjalanan Belajar</h2>
    <p class="text-[#595c5e] text-sm mb-8">Buat akun dan bergabung dengan komunitas pelajar.</p>

    <form wire:submit="register" class="space-y-5">
        <!-- Name -->
        <div>
            <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Nama Lengkap</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#595c5e] text-sm">person</span>
                <input type="text"
                       wire:model="name"
                       placeholder="Nama Anda"
                       autofocus
                       class="w-full pl-12 pr-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 @error('name') ring-2 ring-[#b31b25]/30 @enderror">
            </div>
            @error('name')<p class="text-[#b31b25] text-xs mt-1.5">{{ $message }}</p>@enderror
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Email</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#595c5e] text-sm">mail</span>
                <input type="email"
                       wire:model="email"
                       placeholder="nama@email.com"
                       class="w-full pl-12 pr-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 @error('email') ring-2 ring-[#b31b25]/30 @enderror">
            </div>
            @error('email')<p class="text-[#b31b25] text-xs mt-1.5">{{ $message }}</p>@enderror
        </div>

        <!-- Password -->
        <div>
            <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Kata Sandi</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#595c5e] text-sm">lock</span>
                <input type="password"
                       wire:model="password"
                       placeholder="Min. 8 karakter"
                       class="w-full pl-12 pr-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
            </div>
            @error('password')<p class="text-[#b31b25] text-xs mt-1.5">{{ $message }}</p>@enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Konfirmasi Kata Sandi</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#595c5e] text-sm">lock_reset</span>
                <input type="password"
                       wire:model="password_confirmation"
                       placeholder="Ulangi kata sandi"
                       class="w-full pl-12 pr-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
            </div>
        </div>

        <!-- Submit -->
        <button type="submit"
                class="w-full py-3.5 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-xl hover:scale-[1.01] transition-transform shadow-md shadow-blue-500/20">
            <span wire:loading wire:target="register" class="inline-block animate-spin mr-2">⟳</span>
            Buat Akun
        </button>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-[#595c5e]">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-[#0058ba] font-semibold hover:underline">Masuk di sini</a>
        </p>
    </div>
</div>
