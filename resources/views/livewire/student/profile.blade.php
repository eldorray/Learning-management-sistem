<div class="p-4 md:p-8 lg:p-12 max-w-6xl mx-auto">

    <!-- Flash Message -->
    @if(session('profile_success'))
    <div class="mb-6 bg-[#73f2dd]/30 border border-[#00675c]/20 text-[#00675c] px-4 py-3 rounded-xl flex items-center gap-3">
        <span class="material-symbols-outlined">check_circle</span>
        {{ session('profile_success') }}
    </div>
    @endif

    <!-- Asymmetric Header Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 mb-8 md:mb-12">

        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
                <!-- Hero Gradient -->
                <div class="h-24 bg-gradient-to-br from-[#0058ba] to-[#6c9fff]"></div>

                <div class="px-6 pb-6">
                    <!-- Avatar -->
                    <div class="relative -mt-12 mb-4">
                        <img src="{{ $user->avatar_url }}"
                             alt="{{ $user->name }}"
                             class="w-24 h-24 rounded-2xl border-4 border-white shadow-md object-cover">
                        <div class="absolute bottom-0 right-0 w-6 h-6 bg-[#00675c] rounded-full border-2 border-white flex items-center justify-center">
                            <span class="material-symbols-outlined text-white" style="font-size: 10px;">check</span>
                        </div>
                    </div>

                    <h2 class="font-headline font-extrabold text-2xl text-[#2c2f31]">{{ $user->name }}</h2>
                    <p class="text-[#595c5e] text-sm">{{ $user->email }}</p>

                    @if($user->bio)
                    <p class="text-sm text-[#595c5e] mt-4 leading-relaxed">{{ $user->bio }}</p>
                    @else
                    <p class="text-sm text-[#595c5e]/50 mt-4 italic">Belum ada bio.</p>
                    @endif

                    <!-- XP Badge -->
                    <div class="mt-4 flex items-center gap-2 bg-[#73f2dd]/20 px-4 py-2 rounded-full w-fit">
                        <span class="material-symbols-outlined text-[#00675c] text-sm" style="font-variation-settings: 'FILL' 1;">bolt</span>
                        <span class="text-sm font-bold text-[#00675c]">{{ $user->xp_points }} XP</span>
                    </div>

                    <!-- Streak -->
                    <div class="mt-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-orange-500" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                        <span class="text-sm font-semibold text-[#2c2f31]">{{ $user->streak_days }} hari streak</span>
                    </div>

                    <!-- Student info (read-only) -->
                    @if($user->isStudent() && ($user->nis || $user->nisn || $user->class_group))
                    <div class="mt-4 pt-4 border-t border-[#abadaf]/10 space-y-1.5">
                        @if($user->class_group)
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#595c5e] text-sm">school</span>
                            <span class="text-sm text-[#2c2f31] font-medium">{{ $user->class_group }}</span>
                        </div>
                        @endif
                        @if($user->nis)
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#595c5e] text-sm">badge</span>
                            <span class="text-xs text-[#595c5e]">NIS: <span class="font-mono font-semibold text-[#2c2f31]">{{ $user->nis }}</span></span>
                        </div>
                        @endif
                        @if($user->nisn)
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#595c5e] text-sm">tag</span>
                            <span class="text-xs text-[#595c5e]">NISN: <span class="font-mono font-semibold text-[#2c2f31]">{{ $user->nisn }}</span></span>
                        </div>
                        @endif
                    </div>
                    @endif

                    <div class="mt-6 pt-6 border-t border-[#abadaf]/10 space-y-4">
                        <div>
                            <p class="text-xs text-[#595c5e] uppercase tracking-wider font-semibold mb-1">Bergabung sejak</p>
                            <p class="text-sm font-medium text-[#2c2f31]">{{ $user->created_at?->format('d M Y') }}</p>
                        </div>

                        <!-- Edit Profile Button -->
                        <button wire:click="openEditModal"
                                class="w-full py-3 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.01] transition-transform shadow-sm flex items-center justify-center gap-2 text-sm">
                            <span class="material-symbols-outlined text-sm">edit</span>
                            Edit Profil
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats & Progress -->
        <div class="lg:col-span-2 space-y-6">
            <div>
                <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs">Perjalanan Belajar</span>
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-headline font-extrabold text-[#2c2f31] tracking-tight mt-1">
                    Progres & Pencapaian
                </h1>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
                    <p class="text-2xl md:text-3xl font-headline font-extrabold text-[#0058ba]">{{ $enrollments->count() }}</p>
                    <p class="text-xs text-[#595c5e] mt-1 font-medium">Total Kursus</p>
                </div>
                <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
                    <p class="text-2xl md:text-3xl font-headline font-extrabold text-[#00675c]">{{ $completedCourses->count() }}</p>
                    <p class="text-xs text-[#595c5e] mt-1 font-medium">Selesai</p>
                </div>
                <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
                    <p class="text-2xl md:text-3xl font-headline font-extrabold text-[#00647c]">{{ $totalLessonsCompleted }}</p>
                    <p class="text-xs text-[#595c5e] mt-1 font-medium">Pelajaran</p>
                </div>
                <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center">
                    <p class="text-2xl md:text-3xl font-headline font-extrabold text-[#2c2f31]">{{ $overallProgress }}%</p>
                    <p class="text-xs text-[#595c5e] mt-1 font-medium">Rata-rata Progres</p>
                </div>
            </div>

            <!-- Overall Progress Bar -->
            <div class="bg-white p-6 rounded-xl border border-[#abadaf]/10 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-headline font-bold text-[#2c2f31]">Progres Keseluruhan</h3>
                    <span class="text-[#00675c] font-bold text-lg">{{ $overallProgress }}%</span>
                </div>
                <div class="h-3 bg-[#eef1f3] rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-[#00675c] to-[#4dc9f1] rounded-full transition-all duration-1000"
                         style="width: {{ $overallProgress }}%"></div>
                </div>
                <p class="text-xs text-[#595c5e] mt-2">
                    {{ $completedCourses->count() }} dari {{ $enrollments->count() }} kursus selesai
                </p>
            </div>
        </div>
    </div>

    <!-- Active Courses -->
    @if($activeCourses->count() > 0)
    <section class="mb-6 md:mb-8 space-y-4">
        <h2 class="text-xl md:text-2xl font-headline font-bold text-[#2c2f31]">Kursus Aktif</h2>
        <div class="space-y-3">
            @foreach($activeCourses as $enrollment)
            <div class="bg-white p-5 rounded-xl border border-[#abadaf]/10 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-[#0058ba] to-[#6c9fff] rounded-xl flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-white text-lg">school</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-headline font-bold text-[#2c2f31] truncate">{{ $enrollment->course->title }}</h4>
                        <div class="flex items-center gap-3 mt-2">
                            <div class="flex-1 h-2 bg-[#eef1f3] rounded-full overflow-hidden">
                                <div class="h-full bg-[#00675c] rounded-full transition-all"
                                     style="width: {{ $enrollment->progress_percentage }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-[#00675c] flex-shrink-0">{{ $enrollment->progress_percentage }}%</span>
                        </div>
                    </div>
                    <a href="{{ route('student.learn', $enrollment->course->slug) }}"
                       class="px-4 py-2 bg-[#0058ba] text-[#f0f2ff] text-sm font-bold rounded-full hover:scale-[1.02] transition-transform flex-shrink-0">
                        Lanjutkan
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Completed Courses -->
    @if($completedCourses->count() > 0)
    <section class="space-y-4">
        <h2 class="text-xl md:text-2xl font-headline font-bold text-[#2c2f31]">Kursus Selesai</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($completedCourses as $enrollment)
            <div class="bg-[#73f2dd]/10 p-5 rounded-xl border border-[#00675c]/10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#00675c] rounded-xl flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-white" style="font-variation-settings: 'FILL' 1;">task_alt</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-headline font-bold text-[#2c2f31] truncate">{{ $enrollment->course->title }}</h4>
                        <p class="text-xs text-[#00675c] font-semibold mt-0.5">
                            Selesai {{ $enrollment->completed_at?->format('d M Y') ?? '-' }}
                        </p>
                    </div>
                    <span class="material-symbols-outlined text-[#00675c]" style="font-variation-settings: 'FILL' 1;">verified</span>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ════════════════════════════════════════════════════════
         EDIT PROFILE MODAL
         ════════════════════════════════════════════════════════ --}}
    @if($showEditModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4"
         wire:click.self="$set('showEditModal', false)">
        <div class="bg-white rounded-t-2xl sm:rounded-xl shadow-2xl w-full sm:max-w-2xl max-h-[92vh] overflow-y-auto">
            <div class="w-10 h-1 bg-[#abadaf]/30 rounded-full mx-auto mt-3 sm:hidden"></div>

            {{-- Modal Header --}}
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
                        <img src="{{ auth()->user()->avatar_url }}"
                             alt="avatar"
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

                {{-- Student-only fields --}}
                @if(auth()->user()->isStudent())
                <div class="border-t border-[#eef1f3] pt-5 space-y-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-[#595c5e]">Data Siswa</p>

                    {{-- NIS & NISN (read-only) --}}
                    @if(auth()->user()->nis || auth()->user()->nisn)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if(auth()->user()->nis)
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1">NIS</label>
                            <div class="flex items-center gap-2 px-4 py-3 bg-[#f5f7f9] rounded-xl">
                                <span class="material-symbols-outlined text-[#595c5e] text-sm">lock</span>
                                <span class="text-sm font-mono text-[#595c5e]">{{ auth()->user()->nis }}</span>
                            </div>
                            <p class="text-xs text-[#595c5e]/70 mt-1">Tidak dapat diubah</p>
                        </div>
                        @endif
                        @if(auth()->user()->nisn)
                        <div>
                            <label class="block text-sm font-semibold text-[#2c2f31] mb-1">NISN</label>
                            <div class="flex items-center gap-2 px-4 py-3 bg-[#f5f7f9] rounded-xl">
                                <span class="material-symbols-outlined text-[#595c5e] text-sm">lock</span>
                                <span class="text-sm font-mono text-[#595c5e]">{{ auth()->user()->nisn }}</span>
                            </div>
                            <p class="text-xs text-[#595c5e]/70 mt-1">Tidak dapat diubah</p>
                        </div>
                        @endif
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
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Kelas / Rombel</label>
                        <input type="text" wire:model="class_group" placeholder="Contoh: VII-A"
                               class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Nama Wali / Orang Tua</label>
                        <input type="text" wire:model="guardian_name" placeholder="Nama wali murid"
                               class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#2c2f31] mb-1">Alamat</label>
                        <textarea wire:model="address" rows="2" placeholder="Alamat lengkap"
                                  class="w-full px-4 py-3 bg-[#eef1f3] border-none rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 resize-none"></textarea>
                    </div>
                </div>
                @endif

                {{-- Instructor-only fields --}}
                @if(auth()->user()->isInstructor())
                <div class="border-t border-[#eef1f3] pt-5 space-y-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-[#595c5e]">Data Instruktur</p>

                    {{-- NIP (read-only) --}}
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
