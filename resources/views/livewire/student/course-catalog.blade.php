<div class="p-4 md:p-8 lg:p-12 max-w-7xl mx-auto space-y-6 md:space-y-8">

    <!-- Header -->
    <header class="space-y-2">
        <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs">Pustaka Pengetahuan</span>
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-headline font-extrabold text-[#2c2f31] tracking-tight">
            Katalog Kursus
        </h1>
        <p class="text-[#595c5e] text-base md:text-lg max-w-xl">Temukan kursus yang mengubah cara Anda melihat dunia.</p>
    </header>

    <!-- Flash Message -->
    @if(session('success'))
    <div class="bg-[#73f2dd]/30 border border-[#00675c]/20 text-[#00675c] px-4 py-3 rounded-xl flex items-center gap-3 text-sm">
        <span class="material-symbols-outlined text-sm">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    <!-- Search & Filters -->
    <div class="bg-white p-4 md:p-6 rounded-xl border border-[#abadaf]/10 shadow-sm space-y-3">
        <!-- Search -->
        <div class="relative">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#595c5e]">search</span>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari kursus, topik, atau instruktur..."
                   class="w-full pl-12 pr-4 py-3 bg-[#eef1f3] border-none rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
        </div>

        <!-- Filters Row -->
        <div class="flex gap-2 flex-wrap">
            <select wire:model.live="level"
                    class="flex-1 min-w-[100px] px-3 py-2.5 bg-[#eef1f3] border-none rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-[#2c2f31]">
                <option value="">Semua Level</option>
                <option value="beginner">Pemula</option>
                <option value="intermediate">Menengah</option>
                <option value="advanced">Mahir</option>
            </select>

            <select wire:model.live="category"
                    class="flex-1 min-w-[100px] px-3 py-2.5 bg-[#eef1f3] border-none rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-[#2c2f31]">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>

            <select wire:model.live="sortBy"
                    class="flex-1 min-w-[100px] px-3 py-2.5 bg-[#eef1f3] border-none rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20 text-[#2c2f31]">
                <option value="latest">Terbaru</option>
                <option value="popular">Terpopuler</option>
                <option value="title">Alfabet</option>
            </select>
        </div>
    </div>

    <!-- Results Info -->
    <div class="flex items-center justify-between">
        <p class="text-[#595c5e] text-sm">
            Menampilkan <span class="font-bold text-[#2c2f31]">{{ $courses->total() }}</span> kursus
            @if($search) untuk "<span class="text-[#0058ba]">{{ $search }}</span>"@endif
        </p>
        <div wire:loading class="flex items-center gap-2 text-[#595c5e] text-sm">
            <div class="w-4 h-4 border-2 border-[#0058ba] border-t-transparent rounded-full animate-spin"></div>
            <span class="hidden sm:inline">Memuat...</span>
        </div>
    </div>

    <!-- Course Grid -->
    @if($courses->isEmpty())
    <div class="bg-white rounded-xl border border-[#abadaf]/10 p-12 md:p-16 text-center">
        <div class="w-16 h-16 md:w-20 md:h-20 bg-[#eef1f3] rounded-2xl flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-[#595c5e] text-3xl md:text-4xl">search_off</span>
        </div>
        <h3 class="font-headline font-bold text-xl md:text-2xl text-[#2c2f31] mb-2">Kursus Tidak Ditemukan</h3>
        <p class="text-[#595c5e] text-sm">Coba kata kunci atau filter yang berbeda.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        @foreach($courses as $course)
        <div class="asymmetric-card bg-white overflow-hidden border border-[#abadaf]/10 shadow-sm hover:shadow-md hover:translate-y-[-4px] transition-all duration-300 group">
            <!-- Thumbnail -->
            <div class="relative h-40 md:h-48 bg-gradient-to-br from-[#0058ba] to-[#6c9fff] overflow-hidden">
                @if($course->thumbnail)
                    <img src="{{ asset('storage/'.$course->thumbnail) }}"
                         alt="{{ $course->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                @else
                    <div class="w-full h-full flex items-center justify-center opacity-30">
                        <span class="material-symbols-outlined text-white text-6xl md:text-7xl">school</span>
                    </div>
                @endif

                <div class="absolute top-3 left-3">
                    <span class="bg-white/90 backdrop-blur text-[#0058ba] text-xs font-bold px-2.5 py-1 rounded-full">
                        {{ $course->level_badge }}
                    </span>
                </div>
                @if($course->is_free)
                <div class="absolute top-3 right-3">
                    <span class="bg-[#73f2dd] text-[#00675c] text-xs font-bold px-2.5 py-1 rounded-full">
                        Gratis
                    </span>
                </div>
                @endif
            </div>

            <!-- Content -->
            <div class="p-4 md:p-6 space-y-3 md:space-y-4">
                <div>
                    @if($course->category)
                    <span class="text-xs text-[#00675c] font-semibold uppercase tracking-wider">{{ $course->category }}</span>
                    @endif
                    <h3 class="font-headline font-bold text-base md:text-lg group-hover:text-[#0058ba] transition-colors mt-1 line-clamp-2">
                        {{ $course->title }}
                    </h3>
                    <p class="text-sm text-[#595c5e] mt-1">oleh {{ $course->instructor?->name ?? 'Instruktur' }}</p>
                </div>

                @if($course->short_description)
                <p class="text-sm text-[#595c5e] line-clamp-2 hidden sm:block">{{ $course->short_description }}</p>
                @endif

                <!-- Meta -->
                <div class="flex items-center gap-3 text-xs text-[#595c5e]">
                    <div class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">play_lesson</span>
                        {{ $course->total_lessons }}
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">schedule</span>
                        {{ $course->formatted_duration }}
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">group</span>
                        {{ $course->enrollments_count }}
                    </div>
                </div>

                <!-- Action -->
                @if($completedIds->contains($course->id))
                    <div class="w-full flex items-center justify-center gap-2 py-2.5 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full text-sm cursor-default">
                        <span class="material-symbols-outlined text-sm">verified</span>
                        <span class="hidden sm:inline">Anda sudah menyelesaikan kursus ini</span>
                        <span class="sm:hidden">Selesai</span>
                    </div>
                @elseif($enrolledIds->contains($course->id))
                    <a href="{{ route('student.learn', $course->slug) }}"
                       class="w-full flex items-center justify-center gap-2 py-2.5 bg-[#73f2dd]/30 text-[#00675c] font-bold rounded-full text-sm hover:bg-[#73f2dd]/50 transition-colors">
                        <span class="material-symbols-outlined text-sm">play_circle</span>
                        Lanjutkan Belajar
                    </a>
                @else
                    <button wire:click="openEnrollModal({{ $course->id }})"
                            class="w-full flex items-center justify-center gap-2 py-2.5 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full text-sm hover:scale-[1.02] transition-transform shadow-sm shadow-blue-500/20">
                        <span class="material-symbols-outlined text-sm">vpn_key</span>
                        Masukkan Kode
                    </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $courses->links() }}
    </div>
    @endif

    {{-- ENROLLMENT CODE MODAL --}}
    @if($showCodeModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-0 sm:p-4" wire:click.self="$set('showCodeModal', false)">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl w-full sm:max-w-sm p-6 sm:p-8 text-center">
            <div class="w-10 h-1 bg-[#abadaf]/30 rounded-full mx-auto mb-4 sm:hidden"></div>
            <div class="w-14 h-14 md:w-16 md:h-16 bg-[#0058ba]/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[#0058ba] text-2xl md:text-3xl">vpn_key</span>
            </div>
            <h3 class="font-headline font-bold text-xl text-[#2c2f31] mb-2">Kode Pendaftaran</h3>
            <p class="text-[#595c5e] text-sm mb-6">Masukkan kode yang diberikan oleh instruktur untuk mendaftar kursus ini.</p>

            <div class="mb-4">
                <input type="text"
                       wire:model="enrollmentCode"
                       placeholder="Contoh: A1B2C3"
                       maxlength="8"
                       class="w-full text-center text-xl md:text-2xl font-mono font-bold tracking-[0.3em] uppercase px-4 py-3 md:py-4 bg-[#f5f7f9] border-2 {{ $errors->has('enrollmentCode') ? 'border-[#b31b25]' : 'border-[#abadaf]/20' }} rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0058ba]/30 focus:border-[#0058ba]"
                       autofocus>
                @error('enrollmentCode')
                <p class="text-[#b31b25] text-xs mt-2 flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined" style="font-size: 14px;">error</span>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button wire:click="$set('showCodeModal', false)"
                        class="flex-1 py-3 bg-[#eef1f3] text-[#595c5e] font-bold rounded-full hover:bg-[#dfe3e6] transition-colors text-sm">
                    Batal
                </button>
                <button wire:click="enroll"
                        class="flex-1 py-3 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-white font-bold rounded-full hover:scale-[1.02] transition-transform shadow-sm flex items-center justify-center gap-2 text-sm">
                    <span wire:loading.remove wire:target="enroll" class="material-symbols-outlined text-sm">check</span>
                    <span wire:loading wire:target="enroll" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    Daftar
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
