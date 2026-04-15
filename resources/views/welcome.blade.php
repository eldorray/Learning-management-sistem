<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::get('app_name', 'LMS Arrahmah') }} — Platform Pembelajaran Ar-Rahmah</title>
    @if(\App\Models\Setting::get('app_favicon'))
    <link rel="icon" href="{{ asset('storage/' . \App\Models\Setting::get('app_favicon')) }}" type="image/png">
    @else
    <link rel="icon" href="/icons/icon-192x192.png" type="image/png">
    @endif

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0058ba">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="LMS Arrahmah">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

    <meta name="description"
        content="Platform pembelajaran digital Ar-Rahmah. Kursus berkualitas tinggi dalam bidang Teknologi, Agama, Bahasa, dan Manajemen. Belajar kapan saja, di mana saja.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #0058ba 0%, #004da4 40%, #00214e 100%);
        }

        .hero-glow {
            background: radial-gradient(ellipse at 30% 50%, rgba(108, 159, 255, 0.15) 0%, transparent 70%);
        }

        .hero-glow-2 {
            background: radial-gradient(ellipse at 80% 20%, rgba(77, 201, 241, 0.12) 0%, transparent 60%);
        }

        .float-animation {
            animation: gentle-float 6s ease-in-out infinite;
        }

        .float-animation-delay {
            animation: gentle-float 6s ease-in-out infinite 2s;
        }

        @keyframes gentle-float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-12px);
            }
        }

        @keyframes fade-up {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-up {
            animation: fade-up 0.8s ease-out forwards;
        }

        .animate-fade-up-delay-1 {
            animation: fade-up 0.8s ease-out 0.15s forwards;
            opacity: 0;
        }

        .animate-fade-up-delay-2 {
            animation: fade-up 0.8s ease-out 0.3s forwards;
            opacity: 0;
        }

        .animate-fade-up-delay-3 {
            animation: fade-up 0.8s ease-out 0.45s forwards;
            opacity: 0;
        }

        .category-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 88, 186, 0.12);
        }

        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(44, 47, 49, 0.1);
        }

        .course-card:hover .course-thumb {
            transform: scale(1.05);
        }

        .stat-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .feature-card:hover {
            transform: translateY(-4px);
        }

        .cta-gradient {
            background: linear-gradient(135deg, #0058ba 0%, #6c9fff 50%, #4dc9f1 100%);
        }
    </style>
</head>

<body class="bg-[#f5f7f9] text-[#2c2f31] antialiased">

    {{-- ── PWA Install Banner ───────────────────────────────────── --}}
    <div id="pwa-install-banner"
         class="hidden fixed bottom-0 inset-x-0 z-[9999] items-end justify-center p-4 md:items-center md:p-0">
        <div class="w-full max-w-sm mx-auto bg-[#00214e] text-white rounded-t-2xl md:rounded-2xl shadow-2xl p-5 flex items-center gap-4 border border-white/10 md:mb-6">
            <img src="/icons/icon-72x72.png" alt="LMS Arrahmah"
                 class="w-14 h-14 rounded-xl flex-shrink-0 shadow-md">
            <div class="flex-1 min-w-0">
                <p class="font-bold text-sm leading-snug">Pasang LMS Arrahmah</p>
                <p class="text-xs text-white/60 mt-0.5">Akses lebih cepat, bisa dipakai offline!</p>
            </div>
            <div class="flex flex-col gap-2 flex-shrink-0">
                <button onclick="triggerPwaInstall()"
                    class="bg-[#6c9fff] hover:bg-[#4d80e0] text-[#00214e] font-bold text-xs px-4 py-2 rounded-full transition-colors">
                    Pasang
                </button>
                <button onclick="document.getElementById('pwa-install-banner').remove()"
                    class="text-white/50 hover:text-white/80 text-xs text-center transition-colors">
                    Nanti
                </button>
            </div>
        </div>
    </div>

    @php
        $publishedCourses = \App\Models\Course::where('is_published', true)
            ->with('instructor')
            ->withCount('enrollments')
            ->latest()
            ->take(6)
            ->get();
        $totalStudents = \App\Models\User::where('role', 'student')->count();
        $totalCourses = \App\Models\Course::where('is_published', true)->count();
        $totalLessons = \App\Models\Lesson::count();
        $categories = \App\Models\Course::where('is_published', true)->distinct()->pluck('category')->filter();
    @endphp

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- NAVIGATION -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" id="mainNav">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-20">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-2.5 group">
                    @if (\App\Models\Setting::get('app_logo'))
                        <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}"
                            class="w-9 h-9 md:w-10 md:h-10 rounded-xl object-contain" alt="Logo">
                    @else
                        <div
                            class="w-9 h-9 md:w-10 md:h-10 bg-white/10 backdrop-blur-lg rounded-xl flex items-center justify-center border border-white/20 group-hover:bg-white/20 transition-all">
                            <span class="material-symbols-outlined text-white text-lg md:text-xl">school</span>
                        </div>
                    @endif
                    <div>
                        <h1 class="font-headline font-bold text-base md:text-lg text-white leading-tight">
                            {{ \App\Models\Setting::get('app_name', 'LMS Arrahmah') }}</h1>
                        <p class="text-[9px] md:text-[10px] text-white/50 font-semibold tracking-[0.2em] uppercase">Ar-Rahmah</p>
                    </div>
                </a>

                <!-- Nav Links (Desktop) -->
                <div class="hidden md:flex items-center gap-1">
                    <a href="#courses"
                        class="px-4 py-2 text-sm text-white/70 hover:text-white font-medium rounded-full hover:bg-white/10 transition-all">Kursus</a>
                    <a href="#features"
                        class="px-4 py-2 text-sm text-white/70 hover:text-white font-medium rounded-full hover:bg-white/10 transition-all">Keunggulan</a>
                    <a href="#stats"
                        class="px-4 py-2 text-sm text-white/70 hover:text-white font-medium rounded-full hover:bg-white/10 transition-all">Statistik</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-2 md:gap-3">
                    @auth
                        <a href="{{ auth()->user()->isAdmin() || auth()->user()->isInstructor() ? route('admin.dashboard') : route('student.dashboard') }}"
                            class="px-4 md:px-6 py-2 md:py-2.5 bg-white text-[#0058ba] font-bold text-sm rounded-full hover:scale-[1.03] transition-transform shadow-lg shadow-black/10">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-4 md:px-6 py-2 md:py-2.5 bg-white text-[#0058ba] font-bold text-sm rounded-full hover:scale-[1.03] transition-transform shadow-lg shadow-black/10">
                            Masuk
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- HERO SECTION -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <section class="hero-gradient relative overflow-hidden min-h-[92vh] flex items-center">
        <!-- Background Effects -->
        <div class="absolute inset-0 hero-glow"></div>
        <div class="absolute inset-0 hero-glow-2"></div>

        <!-- Decorative Elements -->
        <div class="absolute top-32 right-[10%] w-72 h-72 bg-[#6c9fff]/8 rounded-full blur-3xl float-animation"></div>
        <div class="absolute bottom-20 left-[5%] w-56 h-56 bg-[#4dc9f1]/8 rounded-full blur-3xl float-animation-delay">
        </div>

        <!-- Grid Pattern Overlay -->
        <div class="absolute inset-0 opacity-[0.03]"
            style="background-image: radial-gradient(circle, rgba(255,255,255,0.8) 1px, transparent 1px); background-size: 40px 40px;">
        </div>

        <div class="max-w-7xl mx-auto px-4 lg:px-8 w-full pt-24 md:pt-28 pb-16 md:pb-20 relative z-10">
            <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                <!-- Left: Content -->
                <div>
                    <div class="animate-fade-up">
                        <span
                            class="inline-flex items-center gap-2 px-3 md:px-4 py-1.5 md:py-2 bg-white/10 backdrop-blur-lg rounded-full border border-white/15 text-white/80 text-xs font-semibold tracking-wider uppercase mb-6 md:mb-8">
                            <span class="w-2 h-2 bg-[#73f2dd] rounded-full animate-pulse"></span>
                            Cognitive Sanctuary
                        </span>
                    </div>

                    <h1
                        class="font-headline font-extrabold text-white leading-[1.08] tracking-tight animate-fade-up-delay-1">
                        <span class="text-4xl md:text-5xl lg:text-[4.2rem] block">Cultivate your</span>
                        <span
                            class="text-4xl md:text-5xl lg:text-[4.2rem] block mt-1 md:mt-2 bg-gradient-to-r from-[#73f2dd] via-[#4dc9f1] to-[#6c9fff] bg-clip-text text-transparent italic">intellectual
                            capital.</span>
                    </h1>

                    <p class="mt-5 md:mt-8 text-base md:text-lg text-white/60 leading-relaxed max-w-lg animate-fade-up-delay-2">
                        Platform pembelajaran digital Ar-Rahmah dengan kurikulum berkualitas tinggi. Dirancang untuk
                        kejelasan, kedalaman, dan penguasaan kognitif.
                    </p>

                    <div class="mt-7 md:mt-10 flex flex-wrap gap-3 md:gap-4 animate-fade-up-delay-3">
                        @auth
                            <a href="{{ route('student.catalog') }}"
                                class="px-8 py-4 bg-white text-[#0058ba] font-bold rounded-full hover:scale-[1.03] transition-transform shadow-xl shadow-black/15 flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-sm">explore</span>
                                Jelajahi Kursus
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                                class="px-8 py-4 bg-white text-[#0058ba] font-bold rounded-full hover:scale-[1.03] transition-transform shadow-xl shadow-black/15 flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-sm">rocket_launch</span>
                                Mulai Belajar Gratis
                            </a>
                            <a href="#courses"
                                class="px-8 py-4 bg-white/10 backdrop-blur-lg text-white font-semibold rounded-full border border-white/20 hover:bg-white/20 transition-all flex items-center gap-2 text-sm">
                                <span class="material-symbols-outlined text-sm">play_circle</span>
                                Lihat Kursus
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Right: Stats Cards -->
                <div class="hidden lg:block">
                    <div class="relative">
                        <!-- Main Stat Block -->
                        <div class="stat-card rounded-3xl p-8 max-w-sm ml-auto float-animation">
                            <div class="flex items-center gap-4 mb-6">
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-[#73f2dd] to-[#00675c] rounded-2xl flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-2xl">auto_awesome</span>
                                </div>
                                <div>
                                    <p class="text-white/50 text-xs font-semibold uppercase tracking-wider">Platform
                                        Stats</p>
                                    <p class="text-white font-headline font-bold text-lg">Live & Growing</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center">
                                    <p class="text-3xl font-headline font-extrabold text-white">{{ $totalCourses }}</p>
                                    <p class="text-white/40 text-xs mt-1">Kursus</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-3xl font-headline font-extrabold text-[#73f2dd]">{{ $totalStudents }}
                                    </p>
                                    <p class="text-white/40 text-xs mt-1">Siswa</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-3xl font-headline font-extrabold text-[#4dc9f1]">{{ $totalLessons }}
                                    </p>
                                    <p class="text-white/40 text-xs mt-1">Pelajaran</p>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Mini Card -->
                        <div
                            class="stat-card rounded-2xl p-5 max-w-[220px] absolute -left-4 top-1/2 -translate-y-1/2 float-animation-delay">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 bg-[#6c9fff]/20 rounded-xl flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[#6c9fff] text-lg">trending_up</span>
                                </div>
                                <div>
                                    <p class="text-white font-bold text-sm">100% Gratis</p>
                                    <p class="text-white/40 text-[10px]">Semua kursus terbuka</p>
                                </div>
                            </div>
                            <div class="h-1.5 bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-[#73f2dd] to-[#4dc9f1] rounded-full"
                                    style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Curve -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                <path
                    d="M0 60L48 55C96 50 192 40 288 38C384 36 480 42 576 50C672 58 768 68 864 68C960 68 1056 58 1152 50C1248 42 1344 36 1392 33L1440 30V100H1392C1344 100 1248 100 1152 100C1056 100 960 100 864 100C768 100 672 100 576 100C480 100 384 100 288 100C192 100 96 100 48 100H0V60Z"
                    fill="#f5f7f9" />
            </svg>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- CATEGORIES -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    @if ($categories->isNotEmpty())
        <section class="py-12 -mt-4 relative z-10">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-center gap-3">
                    @php
                        $categoryIcons = [
                            'Teknologi' => 'code',
                            'Agama' => 'mosque',
                            'Bahasa' => 'translate',
                            'Manajemen' => 'groups',
                        ];
                        $categoryColors = [
                            'Teknologi' => 'from-[#0058ba] to-[#6c9fff]',
                            'Agama' => 'from-[#00675c] to-[#73f2dd]',
                            'Bahasa' => 'from-[#00647c] to-[#4dc9f1]',
                            'Manajemen' => 'from-[#7c3aed] to-[#a78bfa]',
                        ];
                    @endphp
                    @foreach ($categories as $cat)
                        <a href="#courses"
                            class="category-pill flex items-center gap-2.5 px-5 py-3 bg-white rounded-full shadow-sm border border-[#abadaf]/10 transition-all duration-300 group">
                            <div
                                class="w-8 h-8 bg-gradient-to-br {{ $categoryColors[$cat] ?? 'from-[#0058ba] to-[#6c9fff]' }} rounded-lg flex items-center justify-center">
                                <span
                                    class="material-symbols-outlined text-white text-sm">{{ $categoryIcons[$cat] ?? 'category' }}</span>
                            </div>
                            <span
                                class="text-sm font-semibold text-[#2c2f31] group-hover:text-[#0058ba] transition-colors">{{ $cat }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- COURSES -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <section id="courses" class="py-12 md:py-20">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <!-- Section Header -->
            <div class="max-w-2xl mb-10 md:mb-16">
                <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs block mb-3">Knowledge
                    Repository</span>
                <h2
                    class="text-3xl md:text-4xl lg:text-5xl font-headline font-extrabold tracking-tight text-[#2c2f31] leading-tight">
                    Katalog<br>
                    <span
                        class="bg-gradient-to-r from-[#0058ba] to-[#4dc9f1] bg-clip-text text-transparent italic">Pembelajaran</span>
                </h2>
                <p class="text-[#595c5e] mt-4 text-lg leading-relaxed">
                    Kursus-kursus pilihan yang dirancang untuk kejelasan, kedalaman, dan penguasaan kognitif. Pilih
                    jalur belajar Anda.
                </p>
            </div>

            <!-- Courses Grid -->
            @if ($publishedCourses->isNotEmpty())
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($publishedCourses as $course)
                        <div class="course-card bg-white rounded-2xl border border-[#abadaf]/10 overflow-hidden transition-all duration-500 group"
                            style="border-top-left-radius: 3rem;">
                            <!-- Course Thumbnail -->
                            <div
                                class="relative h-48 overflow-hidden bg-gradient-to-br {{ ['from-[#0058ba] to-[#00214e]', 'from-[#00675c] to-[#004d44]', 'from-[#00647c] to-[#003e4e]', 'from-[#7c3aed] to-[#4c1d95]', 'from-[#b31b25] to-[#7f1d1d]'][$loop->index % 5] }}">
                                <!-- Pattern Overlay -->
                                <div class="absolute inset-0 opacity-10"
                                    style="background-image: radial-gradient(circle, rgba(255,255,255,0.8) 1px, transparent 1px); background-size: 24px 24px;">
                                </div>

                                <!-- Course Icon -->
                                <div
                                    class="absolute inset-0 flex items-center justify-center course-thumb transition-transform duration-500">
                                    <div
                                        class="w-20 h-20 bg-white/10 backdrop-blur-md rounded-3xl flex items-center justify-center border border-white/20">
                                        <span class="material-symbols-outlined text-white text-4xl">
                                            {{ ['code', 'mosque', 'translate', 'groups', 'menu_book'][$loop->index % 5] }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Category Badge -->
                                <div class="absolute top-4 right-4">
                                    <span
                                        class="px-3 py-1 bg-white/20 backdrop-blur-lg text-white text-[10px] font-bold uppercase tracking-wider rounded-full border border-white/20">
                                        {{ $course->category ?? 'Umum' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Course Content -->
                            <div class="p-6">
                                <!-- Meta -->
                                <div class="flex items-center gap-3 mb-3">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold
                                {{ $course->level === 'beginner' ? 'bg-green-50 text-green-700' : ($course->level === 'intermediate' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                        {{ $course->level_badge }}
                                    </span>
                                    <span class="text-xs text-[#595c5e] flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">schedule</span>
                                        {{ $course->formatted_duration }}
                                    </span>
                                    <span class="text-xs text-[#595c5e] flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">group</span>
                                        {{ $course->enrollments_count }}
                                    </span>
                                </div>

                                <!-- Title -->
                                <h3
                                    class="font-headline font-bold text-lg text-[#2c2f31] group-hover:text-[#0058ba] transition-colors leading-snug mb-2">
                                    {{ $course->title }}
                                </h3>

                                <!-- Description -->
                                <p class="text-sm text-[#595c5e] leading-relaxed line-clamp-2 mb-4">
                                    {{ $course->short_description }}
                                </p>

                                <!-- Footer -->
                                <div class="flex items-center justify-between pt-4 border-t border-[#eef1f3]">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $course->instructor?->avatar_url }}"
                                            alt="{{ $course->instructor?->name }}"
                                            class="w-7 h-7 rounded-full object-cover">
                                        <span
                                            class="text-xs font-medium text-[#595c5e]">{{ $course->instructor?->name }}</span>
                                    </div>
                                    <span
                                        class="px-3 py-1 bg-[#73f2dd]/30 text-[#00675c] text-xs font-bold rounded-full">
                                        {{ $course->is_free ? 'Gratis' : 'Rp ' . number_format($course->price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- View All Button -->
                <div class="text-center mt-12">
                    @auth
                        <a href="{{ route('student.catalog') }}"
                            class="inline-flex items-center gap-2 px-8 py-4 bg-white text-[#0058ba] font-bold rounded-full shadow-sm border border-[#abadaf]/10 hover:shadow-lg hover:scale-[1.02] transition-all text-sm">
                            Lihat Semua Kursus
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center gap-2 px-8 py-4 bg-white text-[#0058ba] font-bold rounded-full shadow-sm border border-[#abadaf]/10 hover:shadow-lg hover:scale-[1.02] transition-all text-sm">
                            Daftar untuk Akses Semua Kursus
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    @endauth
                </div>
            @else
                <div class="bg-white rounded-3xl p-16 text-center border border-[#abadaf]/10">
                    <div
                        class="w-20 h-20 bg-gradient-to-br from-[#0058ba]/10 to-[#6c9fff]/10 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-[#0058ba] text-4xl">library_books</span>
                    </div>
                    <h3 class="font-headline font-bold text-2xl text-[#2c2f31] mb-3">Segera Hadir</h3>
                    <p class="text-[#595c5e] max-w-md mx-auto">Kursus-kursus berkualitas tinggi sedang dalam proses
                        pembuatan. Daftar sekarang untuk mendapat notifikasi saat kursus tersedia.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- FEATURES -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <section id="features" class="py-12 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <!-- Section Header -->
            <div class="text-center max-w-2xl mx-auto mb-10 md:mb-16">
                <span class="text-[#00675c] font-semibold uppercase tracking-widest text-xs block mb-3">Keunggulan
                    Kami</span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-headline font-extrabold tracking-tight text-[#2c2f31]">
                    Dirancang untuk <span class="text-[#0058ba] italic">fokus</span>
                </h2>
                <p class="text-[#595c5e] mt-3 md:mt-4 text-base md:text-lg">
                    Setiap detail didesain agar proses belajar terasa nyaman, efektif, dan menyenangkan.
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                    $features = [
                        [
                            'icon' => 'psychology',
                            'title' => 'Cognitive Sanctuary',
                            'desc' =>
                                'Ruang belajar digital yang tenang dan fokus, dirancang layaknya perpustakaan premium modern.',
                            'gradient' => 'from-[#0058ba]/10 to-[#6c9fff]/10',
                            'iconColor' => 'text-[#0058ba]',
                        ],
                        [
                            'icon' => 'school',
                            'title' => 'Kurikulum Berkualitas',
                            'desc' =>
                                'Materi disusun oleh pengajar berpengalaman dalam bidang agama, teknologi, bahasa, dan manajemen.',
                            'gradient' => 'from-[#00675c]/10 to-[#73f2dd]/20',
                            'iconColor' => 'text-[#00675c]',
                        ],
                        [
                            'icon' => 'quiz',
                            'title' => 'Kuis Interaktif',
                            'desc' =>
                                'Evaluasi pemahaman dengan kuis pilihan ganda, benar/salah, dan esai yang langsung dinilai.',
                            'gradient' => 'from-amber-500/10 to-amber-300/10',
                            'iconColor' => 'text-amber-600',
                        ],
                        [
                            'icon' => 'trending_up',
                            'title' => 'Lacak Progres',
                            'desc' =>
                                'Pantau perkembangan belajar Anda dengan statistik detail, XP points, dan streak harian.',
                            'gradient' => 'from-[#00647c]/10 to-[#4dc9f1]/10',
                            'iconColor' => 'text-[#00647c]',
                        ],
                        [
                            'icon' => 'devices',
                            'title' => 'Akses di Mana Saja',
                            'desc' =>
                                'Belajar dari perangkat apa pun — desktop, tablet, atau smartphone dengan tampilan responsif.',
                            'gradient' => 'from-purple-500/10 to-purple-300/10',
                            'iconColor' => 'text-purple-600',
                        ],
                        [
                            'icon' => 'volunteer_activism',
                            'title' => '100% Gratis',
                            'desc' =>
                                'Seluruh kursus tersedia secara gratis. Kami percaya ilmu harus dapat diakses oleh semua orang.',
                            'gradient' => 'from-[#b31b25]/10 to-red-300/10',
                            'iconColor' => 'text-[#b31b25]',
                        ],
                    ];
                @endphp

                @foreach ($features as $feature)
                    <div class="feature-card p-7 rounded-2xl bg-[#f5f7f9] transition-all duration-300 group">
                        <div
                            class="w-14 h-14 bg-gradient-to-br {{ $feature['gradient'] }} rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <span
                                class="material-symbols-outlined {{ $feature['iconColor'] }} text-2xl">{{ $feature['icon'] }}</span>
                        </div>
                        <h3 class="font-headline font-bold text-lg text-[#2c2f31] mb-2">{{ $feature['title'] }}</h3>
                        <p class="text-sm text-[#595c5e] leading-relaxed">{{ $feature['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- STATS SECTION -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <section id="stats" class="py-12 md:py-20">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="cta-gradient rounded-[2rem] md:rounded-[2.5rem] p-8 md:p-12 lg:p-16 relative overflow-hidden">
                <!-- Background Patterns -->
                <div class="absolute inset-0 opacity-[0.05]"
                    style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 32px 32px;">
                </div>
                <div class="absolute top-0 right-0 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-60 h-60 bg-[#73f2dd]/10 rounded-full blur-3xl"></div>

                <div class="relative z-10 text-center">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-headline font-extrabold text-white mb-3 md:mb-4 tracking-tight">
                        Bergabung dengan komunitas<br class="hidden sm:block">pembelajar Ar-Rahmah
                    </h2>
                    <p class="text-white/60 text-base md:text-lg max-w-xl mx-auto mb-8 md:mb-12">
                        Setiap hari bertambah siswa yang memulai perjalanan ilmu mereka bersama kami.
                    </p>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6 max-w-3xl mx-auto mb-8 md:mb-12">
                        @php
                            $statItems = [
                                ['value' => $totalCourses, 'label' => 'Kursus Aktif', 'icon' => 'library_books'],
                                ['value' => $totalStudents, 'label' => 'Siswa Terdaftar', 'icon' => 'group'],
                                ['value' => $totalLessons, 'label' => 'Total Pelajaran', 'icon' => 'article'],
                                ['value' => $categories->count(), 'label' => 'Kategori', 'icon' => 'category'],
                            ];
                        @endphp
                        @foreach ($statItems as $stat)
                            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-4 md:p-6 border border-white/10">
                                <span
                                    class="material-symbols-outlined text-white/40 text-xl md:text-2xl mb-1.5 md:mb-2 block">{{ $stat['icon'] }}</span>
                                <p class="text-2xl md:text-3xl lg:text-4xl font-headline font-extrabold text-white">
                                    {{ $stat['value'] }}</p>
                                <p class="text-white/50 text-xs mt-1 font-medium">{{ $stat['label'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    <!-- CTA -->
                    @guest
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center gap-2 px-10 py-4 bg-white text-[#0058ba] font-bold rounded-full hover:scale-[1.03] transition-transform shadow-xl shadow-black/15 text-sm">
                            <span class="material-symbols-outlined text-sm">rocket_launch</span>
                            Mulai Belajar Sekarang
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- TESTIMONIAL / QUOTE -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <section class="py-12 md:py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 lg:px-8 text-center">
            <div
                class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-[#00675c]/10 to-[#73f2dd]/20 rounded-3xl flex items-center justify-center mx-auto mb-6 md:mb-8">
                <span class="material-symbols-outlined text-[#00675c] text-2xl md:text-3xl">format_quote</span>
            </div>
            <blockquote class="text-xl md:text-2xl lg:text-3xl font-headline font-bold text-[#2c2f31] leading-snug italic mb-5 md:mb-6">
                "Barangsiapa menempuh suatu jalan untuk mencari ilmu, maka Allah memudahkan baginya jalan menuju surga."
            </blockquote>
            <cite class="text-[#595c5e] text-sm not-italic">— HR. Muslim</cite>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- FOOTER -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <footer class="bg-[#0b0f10] text-white pt-12 md:pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-12 pb-10 md:pb-12 border-b border-white/10">
                <!-- Brand -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        @if (\App\Models\Setting::get('app_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}"
                                class="w-10 h-10 rounded-xl object-contain" alt="Logo">
                        @else
                            <div class="w-10 h-10 bg-[#0058ba] rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-xl">school</span>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-headline font-bold text-lg">
                                {{ \App\Models\Setting::get('app_name', 'LMS Arrahmah') }}</h3>
                            <p class="text-[10px] text-white/40 font-semibold tracking-[0.2em] uppercase">Ar-Rahmah</p>
                        </div>
                    </div>
                    <p class="text-white/40 text-sm leading-relaxed max-w-md">
                        Elevating education to a high-art experience. Platform pembelajaran Ar-Rahmah untuk mencetak
                        generasi unggul berilmu dan beriman.
                    </p>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="font-headline font-bold text-sm mb-4 text-white/70 uppercase tracking-wider">Jelajahi
                    </h4>
                    <div class="space-y-3">
                        <a href="#courses"
                            class="block text-sm text-white/40 hover:text-white transition-colors">Katalog Kursus</a>
                        <a href="#features"
                            class="block text-sm text-white/40 hover:text-white transition-colors">Keunggulan</a>
                        @guest
                            <a href="{{ route('register') }}"
                                class="block text-sm text-white/40 hover:text-white transition-colors">Daftar Akun</a>
                            <a href="{{ route('login') }}"
                                class="block text-sm text-white/40 hover:text-white transition-colors">Masuk</a>
                        @endguest
                    </div>
                </div>

                <!-- Support -->
                <div>
                    <h4 class="font-headline font-bold text-sm mb-4 text-white/70 uppercase tracking-wider">Dukungan
                    </h4>
                    <div class="space-y-3">
                        <a href="#"
                            class="block text-sm text-white/40 hover:text-white transition-colors">FAQ</a>
                        <a href="#"
                            class="block text-sm text-white/40 hover:text-white transition-colors">Kontak</a>
                        <a href="#"
                            class="block text-sm text-white/40 hover:text-white transition-colors">Kebijakan
                            Privasi</a>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="flex flex-col md:flex-row items-center justify-between pt-8 gap-4">
                <p class="text-white/30 text-xs">© {{ date('Y') }}
                    {{ \App\Models\Setting::get('app_name', 'LMS Arrahmah') }} — Ar-Rahmah. All rights reserved.</p>
                <p class="text-white/20 text-xs">Built with ❤️ by <a href="https://fahmiealkhudhorie.site"
                        target="_blank" class="hover:text-white transition-colors">F.A.K</a></p>
            </div>
        </div>
    </footer>

    <!-- Scroll-based Nav Background Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nav = document.getElementById('mainNav');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 80) {
                    nav.classList.add('bg-[#00214e]/90', 'backdrop-blur-xl', 'shadow-lg');
                } else {
                    nav.classList.remove('bg-[#00214e]/90', 'backdrop-blur-xl', 'shadow-lg');
                }
            });

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>
