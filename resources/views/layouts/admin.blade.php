<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} | {{ \App\Models\Setting::get('app_name', 'LMS Arrahmah') }}</title>
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

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#f5f7f9] text-[#2c2f31] selection:bg-[#6c9fff] selection:text-[#00214e]">

<div x-data="{ mobileOpen: false }" class="flex min-h-screen">

    <!-- Mobile Sidebar Overlay -->
    <div x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileOpen = false"
         class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm md:hidden"
         style="display:none"></div>

    <!-- Admin Sidebar -->
    <aside :class="mobileOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed md:relative md:translate-x-0 z-50 flex flex-col w-72 h-screen py-8 px-6 space-y-8 bg-slate-100 rounded-r-[3rem] top-0 shrink-0 transition-transform duration-300 md:flex overflow-y-auto">

        <!-- Close button (mobile only) -->
        <button @click="mobileOpen = false" class="absolute top-4 right-4 p-2 rounded-full hover:bg-slate-200 md:hidden">
            <span class="material-symbols-outlined text-slate-600">close</span>
        </button>

        <!-- Logo -->
        <div class="flex items-center gap-4 px-2">
            @if(\App\Models\Setting::get('app_logo'))
            <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}" class="w-12 h-12 rounded-xl object-contain" alt="Logo">
            @else
            <div class="w-12 h-12 bg-[#0058ba] rounded-xl flex items-center justify-center text-[#f0f2ff]">
                <span class="material-symbols-outlined text-2xl">school</span>
            </div>
            @endif
            <div>
                <h1 class="font-headline font-bold text-xl text-slate-900">{{ \App\Models\Setting::get('app_name', 'LMS Arrahmah') }}</h1>
                <p class="text-xs text-[#595c5e]">
                    {{ auth()->user()?->isAdmin() ? 'Admin Sanctuary' : 'Instructor Panel' }}
                </p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 space-y-2">
            <a href="{{ route('admin.dashboard') }}" @click="mobileOpen = false"
               class="flex items-center gap-4 px-5 py-4 rounded-full {{ request()->routeIs('admin.dashboard') ? 'bg-white text-[#0058ba] shadow-sm font-bold' : 'text-slate-600 hover:text-[#0058ba] hover:translate-x-1' }} transition-all duration-300">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="text-sm font-medium">Dashboard</span>
            </a>
            <a href="{{ route('admin.courses') }}" @click="mobileOpen = false"
               class="flex items-center gap-4 px-5 py-4 rounded-full {{ request()->routeIs('admin.courses*') ? 'bg-white text-[#0058ba] shadow-sm font-bold' : 'text-slate-600 hover:text-[#0058ba] hover:translate-x-1' }} transition-all duration-300">
                <span class="material-symbols-outlined">library_books</span>
                <span class="text-sm font-medium">{{ auth()->user()?->isAdmin() ? 'Manajemen Kursus' : 'Kursus Saya' }}</span>
            </a>
            <a href="{{ route('admin.students') }}" @click="mobileOpen = false"
               class="flex items-center gap-4 px-5 py-4 rounded-full {{ request()->routeIs('admin.students*') ? 'bg-white text-[#0058ba] shadow-sm font-bold' : 'text-slate-600 hover:text-[#0058ba] hover:translate-x-1' }} transition-all duration-300">
                <span class="material-symbols-outlined">group</span>
                <span class="text-sm font-medium">Direktori Siswa</span>
            </a>
            @if(auth()->user()?->isAdmin())
            <a href="{{ route('admin.instructors') }}" @click="mobileOpen = false"
               class="flex items-center gap-4 px-5 py-4 rounded-full {{ request()->routeIs('admin.instructors*') ? 'bg-white text-[#0058ba] shadow-sm font-bold' : 'text-slate-600 hover:text-[#0058ba] hover:translate-x-1' }} transition-all duration-300">
                <span class="material-symbols-outlined">school</span>
                <span class="text-sm font-medium">Direktori Guru</span>
            </a>
            <a href="{{ route('admin.parents') }}" @click="mobileOpen = false"
               class="flex items-center gap-4 px-5 py-4 rounded-full {{ request()->routeIs('admin.parents*') ? 'bg-white text-[#0058ba] shadow-sm font-bold' : 'text-slate-600 hover:text-[#0058ba] hover:translate-x-1' }} transition-all duration-300">
                <span class="material-symbols-outlined">supervisor_account</span>
                <span class="text-sm font-medium">Direktori Orang Tua</span>
            </a>
            <a href="{{ route('admin.tahfidz') }}" @click="mobileOpen = false"
               class="flex items-center gap-4 px-5 py-4 rounded-full {{ request()->routeIs('admin.tahfidz') ? 'bg-white text-[#0058ba] shadow-sm font-bold' : 'text-slate-600 hover:text-[#0058ba] hover:translate-x-1' }} transition-all duration-300">
                <span class="material-symbols-outlined">menu_book</span>
                <span class="text-sm font-medium">Manajemen Tahfidz</span>
            </a>
            <a href="{{ route('admin.settings') }}" @click="mobileOpen = false"
               class="flex items-center gap-4 px-5 py-4 rounded-full {{ request()->routeIs('admin.settings') ? 'bg-white text-[#0058ba] shadow-sm font-bold' : 'text-slate-600 hover:text-[#0058ba] hover:translate-x-1' }} transition-all duration-300">
                <span class="material-symbols-outlined">settings</span>
                <span class="text-sm font-medium">Pengaturan</span>
            </a>
            @endif
            @if(auth()->user()?->isInstructor())
            <a href="{{ route('admin.tahfidz.halaqoh') }}" @click="mobileOpen = false"
               class="flex items-center gap-4 px-5 py-4 rounded-full {{ request()->routeIs('admin.tahfidz.halaqoh') ? 'bg-white text-[#0058ba] shadow-sm font-bold' : 'text-slate-600 hover:text-[#0058ba] hover:translate-x-1' }} transition-all duration-300">
                <span class="material-symbols-outlined">auto_stories</span>
                <span class="text-sm font-medium">Halaqoh Tahfidz</span>
            </a>
            @endif
            <a href="{{ route('admin.analytics') }}" @click="mobileOpen = false"
               class="flex items-center gap-4 px-5 py-4 rounded-full {{ request()->routeIs('admin.analytics') ? 'bg-white text-[#0058ba] shadow-sm font-bold' : 'text-slate-600 hover:text-[#0058ba] hover:translate-x-1' }} transition-all duration-300">
                <span class="material-symbols-outlined">insights</span>
                <span class="text-sm font-medium">Analitik & Laporan</span>
            </a>
            @if(auth()->user()?->isAdmin())
            <a href="{{ route('student.dashboard') }}" @click="mobileOpen = false"
               class="flex items-center gap-4 px-5 py-4 rounded-full text-slate-600 hover:text-[#0058ba] hover:translate-x-1 transition-all duration-300">
                <span class="material-symbols-outlined">open_in_new</span>
                <span class="text-sm font-medium">Lihat sebagai Siswa</span>
            </a>
            @endif
        </nav>

        <!-- Admin Profile -->
        <div class="mt-auto px-2">
            <div class="bg-[#dfe3e6] p-4 rounded-xl flex items-center gap-3">
                <a href="{{ route('admin.profile') }}" class="flex-shrink-0">
                    <img src="{{ auth()->user()?->avatar_url }}"
                         alt="{{ auth()->user()?->name }}"
                         class="w-10 h-10 rounded-full object-cover hover:ring-2 hover:ring-[#0058ba]/40 transition-all">
                </a>
                <div class="overflow-hidden flex-1">
                    <a href="{{ route('admin.profile') }}" class="hover:text-[#0058ba] transition-colors">
                        <p class="text-sm font-bold truncate">{{ auth()->user()?->name }}</p>
                    </a>
                    <p class="text-xs text-[#595c5e] truncate">{{ ucfirst(auth()->user()?->role) }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1 text-[#595c5e] hover:text-[#b31b25] transition-colors">
                        <span class="material-symbols-outlined text-sm">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Top Bar -->
        <header class="sticky top-0 z-30 flex justify-between items-center w-full px-4 md:px-8 py-3 md:py-4 bg-[#f5f7f9]/90 backdrop-blur-xl border-b border-slate-100/80">
            <div class="flex items-center gap-3">
                <!-- Mobile menu button -->
                <button @click="mobileOpen = true" class="md:hidden p-2 rounded-full hover:bg-slate-200 transition-colors">
                    <span class="material-symbols-outlined text-slate-600">menu</span>
                </button>
                <!-- Logo text mobile -->
                <span class="md:hidden font-headline font-bold text-lg text-[#0058ba]">
                    {{ \App\Models\Setting::get('app_name', 'LMS') }}
                </span>
                <!-- Search desktop -->
                <div class="hidden md:flex items-center gap-6">
                    <livewire:global-search />
                </div>
            </div>
            <div class="flex items-center gap-2 md:gap-4">
                <!-- Search mobile icon -->
                <div class="md:hidden">
                    <livewire:global-search />
                </div>
                <button class="p-2 rounded-full hover:bg-slate-100 transition-colors relative">
                    <span class="material-symbols-outlined text-slate-600 text-xl">notifications</span>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[#b31b25] rounded-full"></span>
                </button>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 pb-6">
            {{ $slot }}
        </main>

    </div>
</div>

@livewireScripts
</body>
</html>
