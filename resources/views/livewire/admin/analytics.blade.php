<div class="p-4 md:p-8 max-w-7xl mx-auto space-y-6 md:space-y-8">

    <!-- Header -->
    <section class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs block mb-2">Insight Platform</span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-headline font-extrabold tracking-tight text-[#2c2f31]">Analitik & Laporan</h2>
            <p class="text-[#595c5e] mt-1 text-sm md:text-base">Data mendalam untuk keputusan yang lebih baik.</p>
        </div>
        <div class="flex gap-3 items-center">
            <label class="text-sm font-semibold text-[#595c5e]">Periode:</label>
            <select wire:model.live="period"
                    class="px-4 py-2.5 bg-white border border-[#abadaf]/20 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#0058ba]/20">
                <option value="7">7 Hari</option>
                <option value="30">30 Hari</option>
                <option value="90">90 Hari</option>
                <option value="365">1 Tahun</option>
            </select>
        </div>
    </section>

    <!-- KPI Cards -->
    <section class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 md:gap-4">
        <div class="bg-white p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center hover:translate-y-[-2px] transition-all">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center mx-auto mb-3 text-[#0058ba]">
                <span class="material-symbols-outlined text-sm">group</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-[#0058ba]">{{ number_format($totalStudents) }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Total Siswa</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center hover:translate-y-[-2px] transition-all">
            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center mx-auto mb-3 text-indigo-600">
                <span class="material-symbols-outlined text-sm">library_books</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-indigo-600">{{ $totalCourses }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Kursus Aktif</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center hover:translate-y-[-2px] transition-all">
            <div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center mx-auto mb-3 text-[#00675c]">
                <span class="material-symbols-outlined text-sm">school</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-[#00675c]">{{ number_format($totalEnrollments) }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Total Pendaftar</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center hover:translate-y-[-2px] transition-all">
            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center mx-auto mb-3 text-green-600">
                <span class="material-symbols-outlined text-sm">task_alt</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-green-600">{{ $completionRate }}%</p>
            <p class="text-xs text-[#595c5e] mt-1">Tingkat Selesai</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center hover:translate-y-[-2px] transition-all">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center mx-auto mb-3 text-[#0058ba]">
                <span class="material-symbols-outlined text-sm">person_add</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-[#0058ba]">+{{ $newStudents }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Siswa Baru</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-[#abadaf]/10 shadow-sm text-center hover:translate-y-[-2px] transition-all">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center mx-auto mb-3 text-amber-600">
                <span class="material-symbols-outlined text-sm">trending_up</span>
            </div>
            <p class="text-2xl font-headline font-extrabold text-amber-600">+{{ $newEnrollments }}</p>
            <p class="text-xs text-[#595c5e] mt-1">Pendaftar Baru</p>
        </div>
    </section>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Course Performance -->
        <section class="lg:col-span-8">
            <h3 class="text-xl font-headline font-bold text-[#2c2f31] mb-4">Performa Kursus</h3>
            <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
                @if($coursePerformance->isEmpty())
                <div class="p-8 text-center text-[#595c5e]">Belum ada data kursus.</div>
                @else
                <div class="overflow-x-auto">
                <div class="divide-y divide-[#f5f7f9] min-w-[480px]">
                    <div class="grid grid-cols-12 gap-4 px-4 md:px-6 py-3 bg-[#f5f7f9]">
                        <div class="col-span-5 text-xs font-bold uppercase tracking-wider text-[#595c5e]">Kursus</div>
                        <div class="col-span-2 text-xs font-bold uppercase tracking-wider text-[#595c5e] text-center">Siswa</div>
                        <div class="col-span-3 text-xs font-bold uppercase tracking-wider text-[#595c5e]">Avg Progres</div>
                        <div class="col-span-2 text-xs font-bold uppercase tracking-wider text-[#595c5e] text-center">Level</div>
                    </div>
                    @foreach($coursePerformance as $course)
                    <div class="grid grid-cols-12 gap-4 items-center px-6 py-4 hover:bg-[#f5f7f9] transition-colors">
                        <div class="col-span-5">
                            <p class="font-semibold text-sm text-[#2c2f31] truncate">{{ $course->title }}</p>
                            <p class="text-xs text-[#595c5e]">{{ $course->category ?? 'Umum' }}</p>
                        </div>
                        <div class="col-span-2 text-center">
                            <span class="font-bold text-[#0058ba]">{{ $course->enrollments_count }}</span>
                        </div>
                        <div class="col-span-3">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1.5 bg-[#eef1f3] rounded-full overflow-hidden">
                                    <div class="h-full bg-[#00675c] rounded-full"
                                         style="width: {{ round($course->avg_progress ?? 0) }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-[#00675c]">{{ round($course->avg_progress ?? 0) }}%</span>
                            </div>
                        </div>
                        <div class="col-span-2 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                {{ $course->level === 'beginner' ? 'bg-green-50 text-green-700' : ($course->level === 'intermediate' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                {{ $course->level_badge }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                </div>
                @endif
            </div>
        </section>

        <!-- Right Column -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Level Distribution -->
            <div>
                <h3 class="text-xl font-headline font-bold text-[#2c2f31] mb-4">Distribusi Level</h3>
                <div class="bg-white p-6 rounded-xl border border-[#abadaf]/10 shadow-sm space-y-4">
                    @php $totalByLevel = $levelDistribution->sum('count'); @endphp
                    @foreach($levelDistribution as $level)
                    @php
                        $pct = $totalByLevel > 0 ? round(($level->count / $totalByLevel) * 100) : 0;
                        $colors = ['beginner' => 'bg-green-500', 'intermediate' => 'bg-amber-500', 'advanced' => 'bg-red-500'];
                        $labels = ['beginner' => 'Pemula', 'intermediate' => 'Menengah', 'advanced' => 'Mahir'];
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-[#2c2f31]">{{ $labels[$level->level] ?? $level->level }}</span>
                            <span class="font-bold text-[#595c5e]">{{ $level->count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="h-2 bg-[#eef1f3] rounded-full overflow-hidden">
                            <div class="h-full {{ $colors[$level->level] ?? 'bg-blue-500' }} rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    @endforeach

                    @if($levelDistribution->isEmpty())
                    <p class="text-sm text-[#595c5e] text-center">Belum ada data.</p>
                    @endif
                </div>
            </div>

            <!-- Top Students -->
            <div>
                <h3 class="text-xl font-headline font-bold text-[#2c2f31] mb-4">Siswa Terbaik</h3>
                <div class="bg-white rounded-xl border border-[#abadaf]/10 shadow-sm overflow-hidden">
                    @if($topStudents->isEmpty())
                    <div class="p-6 text-center text-[#595c5e] text-sm">Belum ada data.</div>
                    @else
                    <div class="divide-y divide-[#f5f7f9]">
                        @foreach($topStudents as $index => $student)
                        <div class="flex items-center gap-3 p-4 hover:bg-[#f5f7f9] transition-colors">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-headline font-bold
                                {{ $index === 0 ? 'bg-amber-100 text-amber-700' : ($index === 1 ? 'bg-slate-100 text-slate-600' : ($index === 2 ? 'bg-orange-100 text-orange-700' : 'bg-[#eef1f3] text-[#595c5e]')) }}">
                                {{ $index + 1 }}
                            </div>
                            <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}"
                                 class="w-9 h-9 rounded-xl object-cover flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-[#2c2f31] truncate">{{ $student->name }}</p>
                                <p class="text-xs text-[#595c5e]">{{ $student->completed_count }} selesai</p>
                            </div>
                            <div class="flex items-center gap-1 text-[#0058ba] flex-shrink-0">
                                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">bolt</span>
                                <span class="text-xs font-bold">{{ $student->xp_points }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Enrollment Trend -->
    <section>
        <h3 class="text-xl font-headline font-bold text-[#2c2f31] mb-4">Tren Pendaftaran (6 Bulan Terakhir)</h3>
        <div class="bg-white p-6 rounded-xl border border-[#abadaf]/10 shadow-sm">
            @if($enrollmentTrend->isEmpty())
            <div class="py-8 text-center text-[#595c5e]">Belum ada data pendaftaran.</div>
            @else
            @php $maxCount = $enrollmentTrend->max('count') ?: 1; @endphp
            <div class="flex items-end justify-around h-48 gap-2">
                @foreach($enrollmentTrend as $item)
                <div class="flex flex-col items-center gap-2 flex-1">
                    <span class="text-xs font-bold text-[#0058ba]">{{ $item->count }}</span>
                    <div class="w-full bg-[#eef1f3] rounded-t-lg overflow-hidden relative"
                         style="height: {{ round(($item->count / $maxCount) * 160) }}px; min-height: 8px;">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0058ba] to-[#6c9fff] rounded-t-lg"></div>
                    </div>
                    <span class="text-xs text-[#595c5e] text-center leading-tight">{{ $item->label }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

</div>
