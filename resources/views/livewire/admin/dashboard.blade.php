<div class="p-4 md:p-8 max-w-7xl mx-auto space-y-6 md:space-y-8">

    <!-- Header Section -->
    <section class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs block mb-2">Admin Sanctuary</span>
            <h2 class="text-2xl md:text-4xl font-headline font-extrabold tracking-tight text-[#2c2f31]">Institutional Pulse</h2>
            <p class="text-[#595c5e] mt-1 text-sm md:text-lg">Gambaran menyeluruh ekosistem akademik Anda.</p>
        </div>
        <div class="flex gap-2 md:gap-3">
            <a href="{{ route('admin.courses.create') }}"
               class="px-4 md:px-6 py-2.5 bg-[#eef1f3] text-[#0058ba] font-bold rounded-full hover:scale-[1.02] transition-transform flex items-center gap-1.5 text-sm">
                <span class="material-symbols-outlined text-lg">file_download</span>
                <span class="hidden sm:inline">Export</span>
            </a>
            <a href="{{ route('admin.courses.create') }}"
               class="px-4 md:px-6 py-2.5 bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] font-bold rounded-full hover:scale-[1.02] transition-transform shadow-lg shadow-blue-500/20 flex items-center gap-1.5 text-sm">
                <span class="material-symbols-outlined text-lg">add</span>
                <span class="hidden sm:inline">Kursus Baru</span>
                <span class="sm:hidden">Buat</span>
            </a>
        </div>
    </section>

    <!-- Metrics Grid -->
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6">
        <!-- Total Students -->
        <div class="bg-white p-4 md:p-6 rounded-xl border border-[#abadaf]/10 shadow-sm hover:translate-y-[-4px] transition-all duration-300">
            <div class="flex justify-between items-start mb-3 md:mb-4">
                <div class="w-9 h-9 md:w-12 md:h-12 bg-blue-50 rounded-lg flex items-center justify-center text-[#0058ba]">
                    <span class="material-symbols-outlined text-lg md:text-2xl">group</span>
                </div>
                <span class="text-[#00675c] font-bold text-[10px] md:text-xs bg-[#73f2dd]/30 px-1.5 md:px-2 py-0.5 md:py-1 rounded-full text-center">
                    +{{ $newStudentsThisMonth }}
                </span>
            </div>
            <p class="text-xs text-[#595c5e]">Total Siswa</p>
            <h3 class="text-xl md:text-3xl font-headline font-bold mt-0.5">{{ number_format($totalStudents) }}</h3>
        </div>

        <!-- Active Courses -->
        <div class="bg-white p-4 md:p-6 rounded-xl border border-[#abadaf]/10 shadow-sm hover:translate-y-[-4px] transition-all duration-300">
            <div class="flex justify-between items-start mb-3 md:mb-4">
                <div class="w-9 h-9 md:w-12 md:h-12 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                    <span class="material-symbols-outlined text-lg md:text-2xl">library_books</span>
                </div>
                <span class="text-[#595c5e] font-bold text-[10px] md:text-xs bg-[#eef1f3] px-1.5 md:px-2 py-0.5 md:py-1 rounded-full">
                    {{ $publishedCourses }} aktif
                </span>
            </div>
            <p class="text-xs text-[#595c5e]">Total Kursus</p>
            <h3 class="text-xl md:text-3xl font-headline font-bold mt-0.5">{{ $totalCourses }}</h3>
        </div>

        <!-- Total Enrollments -->
        <div class="bg-white p-4 md:p-6 rounded-xl border border-[#abadaf]/10 shadow-sm hover:translate-y-[-4px] transition-all duration-300">
            <div class="flex justify-between items-start mb-3 md:mb-4">
                <div class="w-9 h-9 md:w-12 md:h-12 bg-teal-50 rounded-lg flex items-center justify-center text-[#00675c]">
                    <span class="material-symbols-outlined text-lg md:text-2xl">school</span>
                </div>
                <span class="text-[#0058ba] font-bold text-[10px] md:text-xs bg-blue-50 px-1.5 md:px-2 py-0.5 md:py-1 rounded-full">
                    Daftar
                </span>
            </div>
            <p class="text-xs text-[#595c5e]">Total Pendaftar</p>
            <h3 class="text-xl md:text-3xl font-headline font-bold mt-0.5">{{ number_format($totalEnrollments) }}</h3>
        </div>

        <!-- Completion Rate -->
        <div class="bg-white p-4 md:p-6 rounded-xl border border-[#abadaf]/10 shadow-sm hover:translate-y-[-4px] transition-all duration-300">
            <div class="flex justify-between items-start mb-3 md:mb-4">
                <div class="w-9 h-9 md:w-12 md:h-12 bg-green-50 rounded-lg flex items-center justify-center text-green-600">
                    <span class="material-symbols-outlined text-lg md:text-2xl">task_alt</span>
                </div>
                <span class="text-green-600 font-bold text-[10px] md:text-xs bg-green-50 px-1.5 md:px-2 py-0.5 md:py-1 rounded-full">
                    Selesai
                </span>
            </div>
            <p class="text-xs text-[#595c5e]">Penyelesaian</p>
            <h3 class="text-xl md:text-3xl font-headline font-bold mt-0.5">{{ $completionRate }}%</h3>
        </div>
    </section>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
        <!-- Avg Progress -->
        <div class="bg-gradient-to-br from-[#0058ba] to-[#004da4] p-5 md:p-6 rounded-xl text-[#f0f2ff]">
            <p class="text-sm opacity-80 mb-1">Rata-rata Progres Siswa</p>
            <p class="text-3xl md:text-4xl font-headline font-extrabold">{{ round($avgProgress) }}%</p>
            <div class="mt-3 h-2 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-white/70 rounded-full" style="width: {{ round($avgProgress) }}%"></div>
            </div>
        </div>

        <!-- Completed This Month -->
        <div class="bg-white p-5 md:p-6 rounded-xl border border-[#abadaf]/10 shadow-sm">
            <p class="text-sm text-[#595c5e] mb-1">Selesai Bulan Ini</p>
            <p class="text-3xl md:text-4xl font-headline font-extrabold text-[#00675c]">
                {{ \App\Models\Enrollment::where('status', 'completed')->whereMonth('updated_at', now()->month)->count() }}
            </p>
            <p class="text-xs text-[#595c5e] mt-2">kursus diselesaikan</p>
        </div>

        <!-- New This Month -->
        <div class="bg-white p-5 md:p-6 rounded-xl border border-[#abadaf]/10 shadow-sm">
            <p class="text-sm text-[#595c5e] mb-1">Siswa Baru Bulan Ini</p>
            <p class="text-3xl md:text-4xl font-headline font-extrabold text-[#0058ba]">{{ $newStudentsThisMonth }}</p>
            <p class="text-xs text-[#595c5e] mt-2">bergabung baru</p>
        </div>
    </div>

    <!-- Main Grid: Recent + Popular -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">

        <!-- Recent Enrollments -->
        <section class="lg:col-span-7">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg md:text-xl font-headline font-bold text-[#2c2f31]">Pendaftaran Terbaru</h3>
                <a href="{{ route('admin.students') }}" class="text-[#0058ba] text-sm font-semibold hover:underline">Lihat Semua</a>
            </div>
            <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
                @if($recentEnrollments->isEmpty())
                <div class="p-8 text-center text-[#595c5e]">Belum ada pendaftaran.</div>
                @else
                <div class="divide-y divide-[#eef1f3]">
                    @foreach($recentEnrollments as $enrollment)
                    <div class="flex items-center gap-3 p-4 hover:bg-[#f5f7f9] transition-colors">
                        <img src="{{ $enrollment->user->avatar_url }}"
                             alt="{{ $enrollment->user->name }}"
                             class="w-9 h-9 md:w-10 md:h-10 rounded-full object-cover flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-[#2c2f31] truncate">{{ $enrollment->user->name }}</p>
                            <p class="text-xs text-[#595c5e] truncate">{{ $enrollment->course->title }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="flex items-center gap-1.5 justify-end">
                                <div class="w-12 md:w-16 h-1.5 bg-[#eef1f3] rounded-full overflow-hidden">
                                    <div class="h-full bg-[#00675c] rounded-full"
                                         style="width: {{ $enrollment->progress_percentage }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-[#00675c]">{{ $enrollment->progress_percentage }}%</span>
                            </div>
                            <p class="text-xs text-[#595c5e] mt-1 hidden sm:block">{{ $enrollment->enrolled_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </section>

        <!-- Popular Courses -->
        <section class="lg:col-span-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg md:text-xl font-headline font-bold text-[#2c2f31]">Kursus Populer</h3>
                <a href="{{ route('admin.courses') }}" class="text-[#0058ba] text-sm font-semibold hover:underline">Kelola</a>
            </div>
            <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
                @if($popularCourses->isEmpty())
                <div class="p-8 text-center text-[#595c5e]">Belum ada kursus.</div>
                @else
                <div class="divide-y divide-[#eef1f3]">
                    @foreach($popularCourses as $index => $course)
                    <div class="flex items-center gap-3 p-4 hover:bg-[#f5f7f9] transition-colors">
                        <div class="w-8 h-8 bg-[#eef1f3] rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-headline font-bold text-[#595c5e]">{{ $index + 1 }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-[#2c2f31] truncate">{{ $course->title }}</p>
                            <p class="text-xs text-[#595c5e]">{{ $course->level_badge }}</p>
                        </div>
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <span class="material-symbols-outlined text-[#595c5e] text-sm">group</span>
                            <span class="text-sm font-bold text-[#2c2f31]">{{ $course->enrollments_count }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="mt-4 grid grid-cols-2 gap-3">
                <a href="{{ route('admin.courses.create') }}"
                   class="bg-white p-4 rounded-xl border border-[#abadaf]/10 shadow-sm hover:shadow-md transition-all text-center group">
                    <span class="material-symbols-outlined text-[#0058ba] group-hover:scale-110 transition-transform inline-block">add_circle</span>
                    <p class="text-xs font-semibold text-[#2c2f31] mt-1">Tambah Kursus</p>
                </a>
                <a href="{{ route('admin.analytics') }}"
                   class="bg-white p-4 rounded-xl border border-[#abadaf]/10 shadow-sm hover:shadow-md transition-all text-center group">
                    <span class="material-symbols-outlined text-[#00675c] group-hover:scale-110 transition-transform inline-block">insights</span>
                    <p class="text-xs font-semibold text-[#2c2f31] mt-1">Lihat Analitik</p>
                </a>
            </div>
        </section>
    </div>

    <!-- Login Activity Log -->
    <section>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg md:text-xl font-headline font-bold text-[#2c2f31] flex items-center gap-2">
                <span class="material-symbols-outlined text-[#0058ba]">passkey</span>
                Aktivitas Login
            </h3>
            <span class="text-xs text-[#595c5e]">10 terakhir</span>
        </div>
        <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
            @if($loginLogs->isEmpty())
            <div class="p-8 text-center">
                <div class="w-12 h-12 bg-[#eef1f3] rounded-xl flex items-center justify-center mx-auto mb-3">
                    <span class="material-symbols-outlined text-[#595c5e] text-xl">history</span>
                </div>
                <p class="text-[#595c5e] text-sm">Belum ada aktivitas login tercatat.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[400px]">
                    <thead>
                        <tr class="bg-[#f5f7f9]">
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-[#595c5e]">Pengguna</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-[#595c5e] hidden sm:table-cell">Role</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-[#595c5e] hidden lg:table-cell">IP</th>
                            <th class="text-left px-4 py-3 text-xs font-bold uppercase tracking-wider text-[#595c5e] hidden md:table-cell">Perangkat</th>
                            <th class="text-right px-4 py-3 text-xs font-bold uppercase tracking-wider text-[#595c5e]">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#eef1f3]">
                        @foreach($loginLogs as $log)
                        <tr class="hover:bg-[#f5f7f9] transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="relative flex-shrink-0">
                                        <img src="{{ $log->user->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
                                        <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-[#00675c] border-2 border-white rounded-full"></div>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-[#2c2f31] truncate">{{ $log->user->name }}</p>
                                        <p class="text-xs text-[#595c5e] truncate hidden sm:block">{{ $log->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                                @php $r = $log->user->role; @endphp
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $r === 'admin' ? 'bg-purple-50 text-purple-700' : ($r === 'instructor' ? 'bg-blue-50 text-[#0058ba]' : 'bg-[#73f2dd]/30 text-[#00675c]') }}">
                                    {{ ucfirst($r) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <code class="text-xs text-[#595c5e] bg-[#f5f7f9] px-2 py-0.5 rounded">{{ $log->ip_address }}</code>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[#595c5e]" style="font-size: 14px;">
                                        @switch($log->device)
                                            @case('iPhone') @case('iPad') @case('Android') smartphone @break
                                            @case('Mac') laptop_mac @break
                                            @case('Windows') laptop_windows @break
                                            @default devices
                                        @endswitch
                                    </span>
                                    <span class="text-xs text-[#595c5e]">{{ $log->device }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <p class="text-xs font-semibold text-[#2c2f31]">{{ $log->logged_in_at->diffForHumans() }}</p>
                                <p class="text-[10px] text-[#595c5e] hidden sm:block">{{ $log->logged_in_at->format('d M Y, H:i') }}</p>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </section>

</div>
