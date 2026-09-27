<div class="bg-white rounded-2xl shadow-xl border border-[#bfb5a3]/10 p-6 md:p-8">
    <h2 class="text-2xl font-headline font-extrabold text-[#2b2721] mb-2">Selamat Datang Kembali</h2>
    <p class="text-[#6b6358] text-sm mb-8">Masuk ke ruang belajar Anda.</p>

    @if (session('status'))
    <div role="status" class="mb-6 bg-[#f7f0e4] border border-[#d9b98f]/60 text-[#6f4826] px-4 py-3 rounded-xl text-sm">{{ session('status') }}</div>
    @endif

    <form wire:submit="login" class="space-y-5">
        <!-- Email / NISN -->
        <div>
            <label class="block text-sm font-semibold text-[#2b2721] mb-1.5">Email atau NISN</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#6b6358] text-sm">person</span>
                <input type="text"
                       wire:model="identifier"
                       placeholder="nama@email.com atau NISN"
                       autofocus
                       autocomplete="username"
                       class="w-full pl-12 pr-4 py-3 bg-[#ebe5d8] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8a5a31]/20 @error('identifier') ring-2 ring-[#a3402c]/30 @enderror">
            </div>
            @error('identifier')
            <p class="text-[#a3402c] text-xs mt-1.5 flex items-center gap-1">
                <span class="material-symbols-outlined text-xs">error</span>
                {{ $message }}
            </p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label class="block text-sm font-semibold text-[#2b2721] mb-1.5">Kata Sandi</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#6b6358] text-sm">lock</span>
                <input type="{{ $showPassword ? 'text' : 'password' }}"
                       wire:model="password"
                       placeholder="••••••••"
                       autocomplete="current-password"
                       class="w-full pl-12 pr-12 py-3 bg-[#ebe5d8] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#8a5a31]/20 @error('password') ring-2 ring-[#a3402c]/30 @enderror">
                <button type="button"
                        wire:click="togglePassword"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-[#6b6358] hover:text-[#2b2721] transition-colors"
                        aria-label="{{ $showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi' }}">
                    <span class="material-symbols-outlined text-sm">
                        {{ $showPassword ? 'visibility_off' : 'visibility' }}
                    </span>
                </button>
            </div>
            @error('password')
            <p class="text-[#a3402c] text-xs mt-1.5 flex items-center gap-1">
                <span class="material-symbols-outlined text-xs">error</span>
                {{ $message }}
            </p>
            @enderror
        </div>

        <!-- Remember -->
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" wire:model="remember"
                   class="rounded border-[#bfb5a3] text-[#8a5a31] focus:ring-[#8a5a31]/20">
            <span class="text-sm text-[#6b6358]">Ingat saya</span>
        </label>

        <!-- Submit -->
        <button type="submit"
                class="w-full py-3.5 bg-gradient-to-br from-[#8a5a31] to-[#6f4826] text-[#fbf6ee] font-bold rounded-xl hover:scale-[1.01] transition-transform shadow-md shadow-blue-500/20">
            <span wire:loading wire:target="login" class="inline-block animate-spin mr-2">⟳</span>
            Masuk
        </button>
    </form>

    <div class="mt-6 text-center space-y-1">
        <p class="text-sm text-[#6b6358]">
            <span class="font-semibold text-[#2b2721]">Lupa kata sandi atau belum punya akun?</span><br>
            Akun dibuat oleh sekolah. Hubungi admin atau wali kelas untuk membuat akun atau mengatur ulang kata sandi.
        </p>
    </div>
</div>
