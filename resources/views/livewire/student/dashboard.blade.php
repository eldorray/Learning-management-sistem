<div class="p-4 md:p-8 lg:p-12 max-w-7xl mx-auto space-y-8 md:space-y-12">

    <!-- Hero Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div class="space-y-2">
            <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs">Cognitive Sanctuary</span>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-headline font-extrabold text-[#2c2f31] tracking-tight leading-tight">
                Selamat datang, <br><span class="text-[#0058ba]">{{ $user->name }}</span>
            </h1>
            <p class="text-[#595c5e] text-base md:text-lg max-w-md mt-2">
                Fokus adalah aset terbesar Anda.
                @if($totalEnrolled > 0)
                    Anda telah mendaftar {{ $totalEnrolled }} kursus.
                @else
                    Mulai perjalanan belajar Anda hari ini.
                @endif
            </p>
        </div>

        <!-- Stats Card -->
        <div class="w-full md:w-auto bg-[#eef1f3] p-4 md:p-6 rounded-xl flex items-center gap-4 md:gap-6 border border-[#abadaf]/10">
            <div class="text-center flex-1 md:flex-none">
                <p class="text-xl md:text-2xl font-bold font-headline text-[#0058ba]">{{ $user->streak_days }}</p>
                <p class="text-xs text-[#595c5e] font-medium">Hari Streak</p>
            </div>
            <div class="w-px h-10 bg-[#abadaf]/20"></div>
            <div class="text-center flex-1 md:flex-none">
                <p class="text-xl md:text-2xl font-bold font-headline text-[#00675c]">{{ $user->xp_points }}</p>
                <p class="text-xs text-[#595c5e] font-medium">Focus XP</p>
            </div>
            <div class="w-px h-10 bg-[#abadaf]/20"></div>
            <div class="text-center flex-1 md:flex-none">
                <p class="text-xl md:text-2xl font-bold font-headline text-[#2c2f31]">{{ $completedCount }}</p>
                <p class="text-xs text-[#595c5e] font-medium">Selesai</p>
            </div>
        </div>
    </header>

    <!-- Stats Grid -->
    <section class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm">
            <div class="w-9 h-9 md:w-10 md:h-10 bg-blue-50 rounded-lg flex items-center justify-center text-[#0058ba] mb-3">
                <span class="material-symbols-outlined text-lg">auto_stories</span>
            </div>
            <p class="text-xs text-[#595c5e]">Terdaftar</p>
            <p class="text-xl md:text-2xl font-headline font-bold mt-0.5">{{ $totalEnrolled }}</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm">
            <div class="w-9 h-9 md:w-10 md:h-10 bg-teal-50 rounded-lg flex items-center justify-center text-[#00675c] mb-3">
                <span class="material-symbols-outlined text-lg">task_alt</span>
            </div>
            <p class="text-xs text-[#595c5e]">Selesai</p>
            <p class="text-xl md:text-2xl font-headline font-bold mt-0.5">{{ $completedCount }}</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm">
            <div class="w-9 h-9 md:w-10 md:h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600 mb-3">
                <span class="material-symbols-outlined text-lg">trending_up</span>
            </div>
            <p class="text-xs text-[#595c5e]">Sedang Belajar</p>
            <p class="text-xl md:text-2xl font-headline font-bold mt-0.5">{{ $inProgressCount }}</p>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-[#abadaf]/10 shadow-sm">
            <div class="w-9 h-9 md:w-10 md:h-10 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600 mb-3">
                <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' 1;">bolt</span>
            </div>
            <p class="text-xs text-[#595c5e]">XP Points</p>
            <p class="text-xl md:text-2xl font-headline font-bold mt-0.5">{{ $user->xp_points }}</p>
        </div>
    </section>

    <!-- Main Grid: Courses + Sidebar -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">

        <!-- Currently Learning -->
        <section class="lg:col-span-8 space-y-4 md:space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl md:text-2xl font-headline font-bold text-[#2c2f31]">Sedang Dipelajari</h2>
                <a href="{{ route('student.courses') }}" class="text-[#0058ba] font-semibold text-sm hover:underline">Lihat Semua</a>
            </div>

            @if($enrollments->isEmpty())
                <div class="bg-white rounded-xl border border-[#abadaf]/10 p-10 md:p-12 text-center">
                    <div class="w-14 h-14 md:w-16 md:h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-[#0058ba] text-2xl md:text-3xl">school</span>
                    </div>
                    <h3 class="font-headline font-bold text-lg md:text-xl text-[#2c2f31] mb-2">Mulai Perjalanan Belajar</h3>
                    <p class="text-[#595c5e] mb-6 text-sm">Temukan kursus yang sesuai dengan tujuan Anda.</p>
                    <a href="{{ route('student.catalog') }}" class="btn-primary">
                        <span class="material-symbols-outlined text-sm">explore</span>
                        Jelajahi Katalog
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
                    @foreach($enrollments as $enrollment)
                    <div class="asymmetric-card bg-white overflow-hidden border border-[#abadaf]/10 shadow-sm hover:shadow-md transition-all duration-300 group">
                        <!-- Thumbnail -->
                        <div class="relative h-36 md:h-44 bg-gradient-to-br from-[#0058ba] to-[#6c9fff] overflow-hidden">
                            @if($enrollment->course->thumbnail)
                                <img src="{{ asset('storage/'.$enrollment->course->thumbnail) }}"
                                     alt="{{ $enrollment->course->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white/50 text-5xl md:text-6xl">school</span>
                                </div>
                            @endif
                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur px-2.5 py-1 rounded-full text-xs font-bold text-[#0058ba]">
                                {{ $enrollment->course->level_badge }}
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-4 md:p-6 space-y-3 md:space-y-4">
                            <h3 class="text-base md:text-lg font-headline font-bold group-hover:text-[#0058ba] transition-colors line-clamp-2">
                                {{ $enrollment->course->title }}
                            </h3>
                            <p class="text-xs text-[#595c5e]">oleh {{ $enrollment->course->instructor?->name ?? 'Instruktur' }}</p>

                            <!-- Progress -->
                            <div class="space-y-1.5">
                                <div class="flex justify-between text-sm">
                                    <span class="text-[#595c5e] text-xs">Progres Kursus</span>
                                    <span class="font-bold text-[#00675c] text-xs">{{ $enrollment->progress_percentage }}%</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                </div>
                            </div>

                            <a href="{{ route('student.learn', $enrollment->course->slug) }}"
                               class="w-full flex items-center justify-center gap-2 py-2.5 bg-[#eef1f3] hover:bg-gradient-to-br hover:from-[#0058ba] hover:to-[#004da4] text-[#0058ba] hover:text-[#f0f2ff] font-bold rounded-full transition-all duration-300 text-sm">
                                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">play_circle</span>
                                Lanjutkan Belajar
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- Recommended Courses Sidebar -->
        <section class="lg:col-span-4 space-y-4 md:space-y-6">
            <h2 class="text-xl md:text-2xl font-headline font-bold text-[#2c2f31]">Rekomendasi</h2>

            <div class="space-y-3">
                @forelse($recommendedCourses as $course)
                <a href="{{ route('student.catalog') }}"
                   class="block bg-white p-4 rounded-xl border border-[#abadaf]/10 shadow-sm hover:shadow-md hover:translate-y-[-2px] transition-all duration-300 group">
                    <div class="flex gap-3">
                        <div class="w-14 h-14 bg-gradient-to-br from-[#0058ba] to-[#6c9fff] rounded-xl flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-white">school</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-headline font-bold text-sm group-hover:text-[#0058ba] transition-colors line-clamp-2">
                                {{ $course->title }}
                            </h4>
                            <p class="text-xs text-[#595c5e] mt-1">{{ $course->category ?? 'Umum' }}</p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-xs bg-[#73f2dd]/30 text-[#00675c] px-2 py-0.5 rounded-full font-medium">
                                    {{ $course->level_badge }}
                                </span>
                                <span class="text-xs text-[#595c5e]">{{ $course->enrollments_count }} siswa</span>
                            </div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="bg-white p-6 rounded-xl border border-[#abadaf]/10 text-center">
                    <p class="text-[#595c5e] text-sm">Tidak ada rekomendasi tersedia.</p>
                </div>
                @endforelse

                <a href="{{ route('student.catalog') }}"
                   class="block w-full text-center py-3 text-[#0058ba] font-semibold text-sm hover:underline">
                    Lihat Semua Kursus →
                </a>
            </div>

            <!-- Learning Streak -->
            <div class="bg-gradient-to-br from-[#0058ba] to-[#004da4] p-5 md:p-6 rounded-xl text-[#f0f2ff]">
                <div class="flex items-center gap-3 mb-4">
                    <span class="material-symbols-outlined text-yellow-300" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                    <h3 class="font-headline font-bold">Streak Belajar</h3>
                </div>
                <p class="text-3xl md:text-4xl font-headline font-extrabold">{{ $user->streak_days }} <span class="text-lg font-normal opacity-80">hari</span></p>
                <p class="text-sm opacity-70 mt-2">Pertahankan semangat belajar Anda!</p>
                <div class="flex gap-1.5 mt-4">
                    @for($i = 0; $i < 7; $i++)
                    <div class="flex-1 h-2 rounded-full {{ $i < min($user->streak_days, 7) ? 'bg-[#73f2dd]' : 'bg-white/20' }}"></div>
                    @endfor
                </div>
            </div>
        </section>
    </div>
</div>
