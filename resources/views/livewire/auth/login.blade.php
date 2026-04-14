<div class="bg-white rounded-2xl shadow-xl border border-[#abadaf]/10 p-6 md:p-8">
    <h2 class="text-2xl font-headline font-extrabold text-[#2c2f31] mb-2">Selamat Datang Kembali</h2>
    <p class="text-[#595c5e] text-sm mb-8">Masuk ke ruang belajar Anda.</p>

    <form wire:submit="login" class="space-y-5">
        <!-- Email / NISN -->
        <div>
            <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Email atau NISN</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#595c5e] text-sm">person</span>
                <input type="text"
                       wire:model="identifier"
                       placeholder="nama@email.com atau NISN"
                       autofocus
                       autocomplete="username"
                       class="w-full pl-12 pr-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 @error('identifier') ring-2 ring-[#b31b25]/30 @enderror">
            </div>
            @error('identifier')
            <p class="text-[#b31b25] text-xs mt-1.5 flex items-center gap-1">
                <span class="material-symbols-outlined text-xs">error</span>
                {{ $message }}
            </p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label class="block text-sm font-semibold text-[#2c2f31] mb-1.5">Kata Sandi</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#595c5e] text-sm">lock</span>
                <input type="{{ $showPassword ? 'text' : 'password' }}"
                       wire:model="password"
                       placeholder="••••••••"
                       autocomplete="current-password"
                       class="w-full pl-12 pr-12 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 @error('password') ring-2 ring-[#b31b25]/30 @enderror">
                <button type="button"
                        wire:click="togglePassword"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-[#595c5e] hover:text-[#2c2f31] transition-colors"
                        aria-label="{{ $showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi' }}">
                    <span class="material-symbols-outlined text-sm">
                        {{ $showPassword ? 'visibility_off' : 'visibility' }}
                    </span>
                </button>
            </div>
            @error('password')
            <p class="text-[#b31b25] text-xs mt-1.5 flex items-center gap-1">
                <span class="material-symbols-outlined text-xs">error</span>
                {{ $message }}
            </p>
            @enderror
        </div>

        <!-- Remember -->
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" wire:model="remember"
                   class="rounded border-[#abadaf] text-[#0058ba] focus:ring-[#0058ba]/20">
            <span class="text-sm text-[#595c5e]">Ingat saya</span>
        </label>

        <!-- Submit -->
        <button type="submit"
                class="w-full py-3.5 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-xl hover:scale-[1.01] transition-transform shadow-md shadow-blue-500/20">
            <span wire:loading wire:target="login" class="inline-block animate-spin mr-2">⟳</span>
            Masuk
        </button>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-[#595c5e]">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-[#0058ba] font-semibold hover:underline">Daftar sekarang</a>
        </p>
    </div>
</div>
