<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'LMS Arrahmah') }} | {{ \App\Models\Setting::get('app_name', 'LMS Arrahmah') }}</title>
    @if(\App\Models\Setting::get('app_favicon'))
    <link rel="icon" href="{{ asset('storage/' . \App\Models\Setting::get('app_favicon')) }}" type="image/png">
    @endif

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
         class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden"
         style="display:none"></div>

    <!-- Sidebar - Student -->
    <aside :class="mobileOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed lg:relative lg:translate-x-0 z-50 flex flex-col w-64 h-screen top-0 py-8 px-6 space-y-8 bg-slate-100 rounded-r-[3rem] transition-transform duration-300 overflow-y-auto">

        <!-- Close button (mobile only) -->
        <button @click="mobileOpen = false" class="absolute top-4 right-4 p-2 rounded-full hover:bg-slate-200 lg:hidden">
            <span class="material-symbols-outlined text-slate-600">close</span>
        </button>

        <!-- Logo -->
        <div class="flex items-center gap-3 px-2">
            @if(\App\Models\Setting::get('app_logo'))
            <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}" class="w-10 h-10 rounded-xl object-contain" alt="Logo">
            @else
            <div class="w-10 h-10 bg-[#0058ba] rounded-xl flex items-center justify-center text-[#f0f2ff] shadow-lg shadow-blue-500/20">
                <span class="material-symbols-outlined text-xl">school</span>
            </div>
            @endif
            <div>
                <h1 class="font-headline font-bold text-lg text-slate-900 tracking-tight">{{ \App\Models\Setting::get('app_name', 'LMS Arrahmah') }}</h1>
                <p class="text-[10px] text-[#595c5e] uppercase tracking-widest font-bold">{{ \App\Models\Setting::get('app_tagline', 'Platform Pembelajaran') }}</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 space-y-1">
            <a href="{{ route('student.dashboard') }}" @click="mobileOpen = false"
               class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('student.courses') }}" @click="mobileOpen = false"
               class="nav-item {{ request()->routeIs('student.courses') ? 'active' : '' }}">
                <span class="material-symbols-outlined">auto_stories</span>
                <span>Kursus Saya</span>
            </a>
            <a href="{{ route('student.catalog') }}" @click="mobileOpen = false"
               class="nav-item {{ request()->routeIs('student.catalog') ? 'active' : '' }}">
                <span class="material-symbols-outlined">explore</span>
                <span>Katalog</span>
            </a>
            <a href="{{ route('student.tahfidz') }}" @click="mobileOpen = false"
               class="nav-item {{ request()->routeIs('student.tahfidz') ? 'active' : '' }}">
                <span class="material-symbols-outlined">menu_book</span>
                <span>Tahfidz</span>
            </a>
            <a href="{{ route('student.profile') }}" @click="mobileOpen = false"
               class="nav-item {{ request()->routeIs('student.profile') ? 'active' : '' }}">
                <span class="material-symbols-outlined">person</span>
                <span>Profil</span>
            </a>
        </nav>

        <!-- Focus Mode Button -->
        <div class="space-y-3">
            <a href="#" class="w-full bg-gradient-to-br from-[#0058ba] to-[#004da4] text-[#f0f2ff] py-3.5 rounded-full font-bold flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20 hover:scale-[1.02] transition-transform">
                <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">bolt</span>
                <span>Focus Mode</span>
            </a>

            <!-- Divider -->
            <div class="border-t border-[#abadaf]/10 pt-3 space-y-1">
                <a href="#" class="nav-item text-sm">
                    <span class="material-symbols-outlined text-sm">help</span>
                    <span>Bantuan</span>
                </a>

                @if(auth()->user()?->isAdmin())
                <a href="{{ route('admin.dashboard') }}" @click="mobileOpen = false" class="nav-item text-sm">
                    <span class="material-symbols-outlined text-sm">admin_panel_settings</span>
                    <span>Panel Admin</span>
                </a>
                @endif
            </div>

            <!-- User Profile -->
            <div class="bg-[#dfe3e6] p-3 rounded-xl flex items-center gap-3">
                <img src="{{ auth()->user()?->avatar_url }}"
                     alt="{{ auth()->user()?->name }}"
                     class="w-9 h-9 rounded-full object-cover border-2 border-[#6c9fff]/30">
                <div class="overflow-hidden flex-1">
                    <p class="text-sm font-bold truncate">{{ auth()->user()?->name }}</p>
                    <p class="text-xs text-[#595c5e] truncate">{{ auth()->user()?->email }}</p>
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

        <!-- Top Navigation Bar -->
        <nav class="bg-[#f5f7f9]/90 backdrop-blur-xl sticky top-0 z-30 flex justify-between items-center w-full px-4 lg:px-8 py-3 border-b border-slate-100/80">
            <!-- Left: Hamburger + Logo -->
            <div class="flex items-center gap-3">
                <button @click="mobileOpen = true" class="lg:hidden p-2 rounded-full hover:bg-[#e5e9eb] transition-colors">
                    <span class="material-symbols-outlined text-[#595c5e]">menu</span>
                </button>
                <span class="lg:hidden text-lg font-headline font-bold text-[#0058ba]">{{ \App\Models\Setting::get('app_name', 'LMS Arrahmah') }}</span>
            </div>

            <!-- Search (Desktop) -->
            <div class="hidden md:flex items-center gap-4">
                <livewire:global-search />
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-2">
                <!-- Search (Mobile) -->
                <div class="md:hidden">
                    <livewire:global-search />
                </div>

                <!-- XP Badge -->
                <div class="hidden sm:flex items-center gap-1.5 bg-[#73f2dd]/30 px-3 py-1.5 rounded-full">
                    <span class="material-symbols-outlined text-[#00675c] text-sm" style="font-variation-settings: 'FILL' 1;">bolt</span>
                    <span class="text-xs font-bold text-[#00675c]">{{ auth()->user()?->xp_points ?? 0 }} XP</span>
                </div>

                <!-- Notifications -->
                <button class="p-2 rounded-full hover:bg-[#e5e9eb] transition-colors relative">
                    <span class="material-symbols-outlined text-[#595c5e] text-xl">notifications</span>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[#b31b25] rounded-full"></span>
                </button>

                <!-- Avatar -->
                <a href="{{ route('student.profile') }}" class="hidden sm:block">
                    <img src="{{ auth()->user()?->avatar_url }}"
                         alt="{{ auth()->user()?->name }}"
                         class="w-8 h-8 rounded-full object-cover border-2 border-[#0058ba]/20">
                </a>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto pb-20 lg:pb-0">
            {{ $slot }}
        </main>

        <!-- Bottom Navigation (Mobile Only) -->
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-30 bg-white/95 backdrop-blur-xl border-t border-slate-200 flex items-center justify-around px-2 py-2 safe-area-bottom">
            <a href="{{ route('student.dashboard') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-colors {{ request()->routeIs('student.dashboard') ? 'text-[#0058ba]' : 'text-[#595c5e]' }}">
                <span class="material-symbols-outlined text-2xl" style="{{ request()->routeIs('student.dashboard') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">dashboard</span>
                <span class="text-[10px] font-semibold">Home</span>
            </a>
            <a href="{{ route('student.courses') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-colors {{ request()->routeIs('student.courses') ? 'text-[#0058ba]' : 'text-[#595c5e]' }}">
                <span class="material-symbols-outlined text-2xl" style="{{ request()->routeIs('student.courses') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">auto_stories</span>
                <span class="text-[10px] font-semibold">Kursus</span>
            </a>
            <a href="{{ route('student.catalog') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-colors {{ request()->routeIs('student.catalog') ? 'text-[#0058ba]' : 'text-[#595c5e]' }}">
                <span class="material-symbols-outlined text-2xl" style="{{ request()->routeIs('student.catalog') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">explore</span>
                <span class="text-[10px] font-semibold">Katalog</span>
            </a>
            <a href="{{ route('student.tahfidz') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-colors {{ request()->routeIs('student.tahfidz') ? 'text-[#0058ba]' : 'text-[#595c5e]' }}">
                <span class="material-symbols-outlined text-2xl" style="{{ request()->routeIs('student.tahfidz') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">menu_book</span>
                <span class="text-[10px] font-semibold">Tahfidz</span>
            </a>
            <a href="{{ route('student.profile') }}"
               class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition-colors {{ request()->routeIs('student.profile') ? 'text-[#0058ba]' : 'text-[#595c5e]' }}">
                <span class="material-symbols-outlined text-2xl" style="{{ request()->routeIs('student.profile') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">person</span>
                <span class="text-[10px] font-semibold">Profil</span>
            </a>
        </nav>

    </div>
</div>

@livewireScripts
</body>
</html>
