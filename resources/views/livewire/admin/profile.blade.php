<div class="p-4 md:p-8 max-w-4xl mx-auto space-y-6 md:space-y-8">

    <!-- Header -->
    <section>
        <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs block mb-2">
            {{ auth()->user()->isAdmin() ? 'Admin Panel' : 'Instructor Panel' }}
        </span>
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-headline font-extrabold tracking-tight text-[#2c2f31]">Profil Saya</h2>
        <p class="text-[#595c5e] mt-1 text-sm md:text-base">Kelola informasi akun Anda.</p>
    </section>

    <!-- Flash Message -->
    @if(session('profile_success'))
    <div class="bg-[#73f2dd]/30 border border-[#00675c]/20 text-[#00675c] px-4 py-3 rounded-xl flex items-center gap-3">
        <span class="material-symbols-outlined">check_circle</span>
        {{ session('profile_success') }}
    </div>
    @endif

    <!-- Profile Card -->
    <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
        <div class="h-28 bg-gradient-to-br from-[#0058ba] to-[#6c9fff]"></div>

        <div class="px-4 md:px-8 pb-6 md:pb-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 -mt-12 mb-6">
                <div class="flex items-end gap-4">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                         class="w-24 h-24 rounded-2xl border-4 border-white shadow-md object-cover">
                    <div>
                        <h3 class="font-headline font-extrabold text-2xl text-[#2c2f31]">{{ $user->name }}</h3>
                        <p class="text-[#595c5e] text-sm">{{ $user->email }}</p>
                        <span class="mt-1 inline-block px-3 py-0.5 rounded-full text-xs font-bold
                            {{ $user->isAdmin() ? 'bg-[#0058ba]/10 text-[#0058ba]' : 'bg-[#00675c]/10 text-[#00675c]' }}">
                            {{ $user->isAdmin() ? 'Administrator' : 'Instruktur' }}
                        </span>
                    </div>
                </div>
                <button wire:click="openEditModal"
                        class="px-6 py-3 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.02] transition-transform shadow-sm flex items-center gap-2 text-sm self-start md:self-auto">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    Edit Profil
                </button>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($user->bio)
                <div class="md:col-span-2">
                    <p class="text-xs text-[#595c5e] uppercase tracking-wider font-semibold mb-1">Bio</p>
                    <p class="text-sm text-[#2c2f31] leading-relaxed">{{ $user->bio }}</p>
                </div>
                @endif

                <div>
                    <p class="text-xs text-[#595c5e] uppercase tracking-wider font-semibold mb-1">No. Telepon</p>
                    <p class="text-sm text-[#2c2f31] font-medium">{{ $user->phone ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs text-[#595c5e] uppercase tracking-wider font-semibold mb-1">Bergabung Sejak</p>
                    <p class="text-sm text-[#2c2f31] font-medium">{{ $user->created_at?->format('d M Y') }}</p>
                </div>

                @if($user->isInstructor())
                @if($user->nip)
                <div>
                    <p class="text-xs text-[#595c5e] uppercase tracking-wider font-semibold mb-1">NIP</p>
                    <p class="text-sm text-[#2c2f31] font-mono font-medium">{{ $user->nip }}</p>
                </div>
                @endif

                <div>
                    <p class="text-xs text-[#595c5e] uppercase tracking-wider font-semibold mb-1">Bidang Keahlian</p>
                    <p class="text-sm text-[#2c2f31] font-medium">{{ $user->specialization ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs text-[#595c5e] uppercase tracking-wider font-semibold mb-1">Jenis Kelamin</p>
                    <p class="text-sm text-[#2c2f31] font-medium">
                        {{ $user->gender === 'L' ? 'Laki-laki' : ($user->gender === 'P' ? 'Perempuan' : '-') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-[#595c5e] uppercase tracking-wider font-semibold mb-1">Tanggal Lahir</p>
                    <p class="text-sm text-[#2c2f31] font-medium">
                        {{ $user->birth_date ? $user->birth_date->format('d F Y') : '-' }}
                    </p>
                </div>

                <div class="md:col-span-2">
                    <p class="text-xs text-[#595c5e] uppercase tracking-wider font-semibold mb-1">Alamat</p>
                    <p class="text-sm text-[#2c2f31] font-medium">{{ $user->address ?? '-' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════
         EDIT PROFILE MODAL
         ════════════════════════════════════════════════════════ --}}
    @if($showEditModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4"
         wire:click.self="$set('showEditModal', false)">
        <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-2xl w-full sm:max-w-2xl max-h-[92vh] overflow-y-auto">
            <div class="w-10 h-1 bg-[#abadaf]/30 rounded-full mx-auto mt-3 sm:hidden"></div>

            <div class="p-6 border-b border-[#eef1f3] flex items-center justify-between sticky top-0 bg-white z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-[#0058ba] to-[#6c9fff] rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-sm">manage_accounts</span>
                    </div>
                    <h3 class="font-headline font-bold text-xl text-[#2c2f31]">Edit Profil</h3>
                </div>
                <button wire:click="$set('showEditModal', false)"
                        class="p-2 rounded-full hover:bg-[#eef1f3] transition-colors">
                    <span class="material-symbols-outlined text-[#595c5e]">close</span>
                </button>
            </div>

            <form wire:submit="saveProfile" class="p-6 space-y-6">

                {{-- Avatar Upload --}}
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-2">Foto Profil</label>
                    <div class="flex items-center gap-4">
                        <img src="{{ auth()->user()->avatar_url }}" alt="avatar"
                             class="w-16 h-16 rounded-xl object-cover border-2 border-[#eef1f3]">
                        <div class="flex-1">
                            <input type="file" wire:model="avatar" accept="image/*"
                                   class="w-full text-sm text-[#595c5e] file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-[#eef1f3] file:text-[#2c2f31] hover:file:bg-[#dfe3e6] cursor-pointer">
                            <p class="text-xs text-[#595c5e] mt-1">JPG, PNG, atau GIF. Maks 2MB.</p>
                        </div>
                    </div>
                    @error('avatar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Name & Email --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Nama Lengkap *</label>
                        <input type="text" wire:model="name" placeholder="Nama lengkap"
                               class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 @error('name') ring-2 ring-red-300 @enderror">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Email *</label>
                        <input type="email" wire:model="email" placeholder="email@example.com"
                               class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 @error('email') ring-2 ring-red-300 @enderror">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Bio --}}
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Bio</label>
                    <textarea wire:model="bio" rows="3" placeholder="Ceritakan sedikit tentang diri Anda..."
                              class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 resize-none"></textarea>
                    @error('bio')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-semibold text-[#2c2f31] mb-1">No. Telepon</label>
                    <input type="text" wire:model="phone" placeholder="08xxxxxxxxxx"
                           class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Instructor-only fields --}}
                @if(auth()->user()->isInstructor())
                <div class="border-t border-[#eef1f3] pt-5 space-y-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-[#595c5e]">Data Instruktur</p>

                    @if(auth()->user()->nip)
                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">NIP</label>
                        <div class="flex items-center gap-2 px-4 py-3 bg-[#f5f7f9] rounded-xl">
                            <span class="material-symbols-outlined text-[#595c5e] text-sm">lock</span>
                            <span class="text-sm font-mono text-[#595c5e]">{{ auth()->user()->nip }}</span>
                        </div>
                        <p class="text-xs text-[#595c5e]/70 mt-1">Tidak dapat diubah</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Jenis Kelamin</label>
                            <select wire:model="gender"
                                    class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                                <option value="">Pilih...</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Tanggal Lahir</label>
                            <input type="date" wire:model="birth_date"
                                   class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Bidang Keahlian</label>
                        <input type="text" wire:model="specialization" placeholder="Contoh: Matematika, Pemrograman"
                               class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Alamat</label>
                        <textarea wire:model="address" rows="2" placeholder="Alamat lengkap"
                                  class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 resize-none"></textarea>
                    </div>
                </div>
                @endif

                {{-- Change Password --}}
                <div class="border-t border-[#eef1f3] pt-5 space-y-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-[#595c5e]">Ubah Kata Sandi <span class="font-normal normal-case">(kosongkan jika tidak ingin mengubah)</span></p>

                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Kata Sandi Lama</label>
                        <input type="password" wire:model="current_password" placeholder="Kata sandi saat ini"
                               autocomplete="current-password"
                               class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 @error('current_password') ring-2 ring-red-300 @enderror">
                        @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Kata Sandi Baru</label>
                            <input type="password" wire:model="new_password" placeholder="Minimal 6 karakter"
                                   autocomplete="new-password"
                                   class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 @error('new_password') ring-2 ring-red-300 @enderror">
                            @error('new_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Konfirmasi Kata Sandi</label>
                            <input type="password" wire:model="new_password_confirmation" placeholder="Ulangi kata sandi baru"
                                   autocomplete="new-password"
                                   class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-3 pt-2">
                    <button type="button" wire:click="$set('showEditModal', false)"
                            class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-3 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.01] transition-transform shadow-sm">
                        <span wire:loading wire:target="saveProfile" class="inline-block animate-spin mr-1">⟳</span>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
