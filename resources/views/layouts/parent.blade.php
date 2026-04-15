<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Portal Orang Tua' }} | {{ \App\Models\Setting::get('app_name', 'LMS Arrahmah') }}</title>
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#f5f7f9] text-[#2c2f31] selection:bg-[#6c9fff] selection:text-[#00214e]">

<div x-data="{ mobileOpen: false }" class="flex min-h-screen">

    <!-- Mobile Overlay -->
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

    <!-- Sidebar -->
    <aside :class="mobileOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed lg:relative lg:translate-x-0 z-50 flex flex-col w-64 h-screen top-0 py-8 px-6 space-y-6 bg-gradient-to-b from-[#e8f0fe] to-[#f5f7f9] rounded-r-[3rem] transition-transform duration-300 overflow-y-auto shrink-0">

        <!-- Close button mobile -->
        <button @click="mobileOpen = false" class="absolute top-4 right-4 p-2 rounded-full hover:bg-white/60 lg:hidden">
            <span class="material-symbols-outlined text-slate-600">close</span>
        </button>

        <!-- Logo -->
        <div class="flex items-center gap-3 px-2">
            @if(\App\Models\Setting::get('app_logo'))
            <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}" class="w-10 h-10 rounded-xl object-contain" alt="Logo">
            @else
            <div class="w-10 h-10 bg-[#0058ba] rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                <span class="material-symbols-outlined text-xl">family_restroom</span>
            </div>
            @endif
            <div>
                <h1 class="font-headline font-bold text-lg text-slate-900 tracking-tight">{{ \App\Models\Setting::get('app_name', 'LMS Arrahmah') }}</h1>
                <p class="text-[10px] text-[#595c5e] uppercase tracking-widest font-bold">Portal Orang Tua</p>
            </div>
        </div>

        <!-- Read-only badge -->
        <div class="flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl">
            <span class="material-symbols-outlined text-amber-600 text-sm">visibility</span>
            <p class="text-xs font-semibold text-amber-700">Mode Pantau — Hanya Lihat</p>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 space-y-1">
            <a href="{{ route('parent.dashboard') }}" @click="mobileOpen = false"
               class="flex items-center gap-3 px-5 py-3.5 rounded-full text-sm font-medium transition-all duration-300
                {{ request()->routeIs('parent.dashboard') ? 'bg-white text-[#0058ba] shadow-sm font-bold' : 'text-slate-600 hover:text-[#0058ba] hover:translate-x-1' }}">
                <span class="material-symbols-outlined">home</span>
                <span>Dashboard</span>
            </a>
        </nav>

        <!-- Parent Profile -->
        <div class="mt-auto">
            <div class="bg-white/70 p-4 rounded-xl flex items-center gap-3">
                <img src="{{ auth()->user()?->avatar_url }}"
                     alt="{{ auth()->user()?->name }}"
                     class="w-9 h-9 rounded-full object-cover border-2 border-[#6c9fff]/30 flex-shrink-0">
                <div class="overflow-hidden flex-1">
                    <p class="text-sm font-bold truncate">{{ auth()->user()?->name }}</p>
                    <p class="text-xs text-[#595c5e]">Orang Tua / Wali</p>
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
        <nav class="bg-[#f5f7f9]/90 backdrop-blur-xl sticky top-0 z-30 flex justify-between items-center w-full px-4 lg:px-8 py-3 border-b border-slate-100/80">
            <div class="flex items-center gap-3">
                <button @click="mobileOpen = true" class="lg:hidden p-2 rounded-full hover:bg-[#e5e9eb] transition-colors">
                    <span class="material-symbols-outlined text-[#595c5e]">menu</span>
                </button>
                <span class="lg:hidden text-lg font-headline font-bold text-[#0058ba]">
                    {{ \App\Models\Setting::get('app_name', 'LMS Arrahmah') }}
                </span>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-full">
                    <span class="material-symbols-outlined text-amber-600 text-sm">visibility</span>
                    <span class="text-xs font-semibold text-amber-700">Mode Pantau</span>
                </div>
                <button class="p-2 rounded-full hover:bg-[#e5e9eb] transition-colors relative">
                    <span class="material-symbols-outlined text-[#595c5e] text-xl">notifications</span>
                </button>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto pb-20 lg:pb-0">
            {{ $slot }}
        </main>

        <!-- Bottom Navigation (Mobile) -->
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-30 bg-white/95 backdrop-blur-xl border-t border-slate-200 flex items-center justify-around px-2 py-2">
            <a href="{{ route('parent.dashboard') }}"
               class="flex flex-col items-center gap-0.5 px-4 py-1.5 rounded-xl transition-colors {{ request()->routeIs('parent.dashboard') ? 'text-[#0058ba]' : 'text-[#595c5e]' }}">
                <span class="material-symbols-outlined text-2xl" style="{{ request()->routeIs('parent.dashboard') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">home</span>
                <span class="text-[10px] font-semibold">Dashboard</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="flex flex-col items-center">
                @csrf
                <button type="submit" class="flex flex-col items-center gap-0.5 px-4 py-1.5 rounded-xl text-[#595c5e]">
                    <span class="material-symbols-outlined text-2xl">logout</span>
                    <span class="text-[10px] font-semibold">Keluar</span>
                </button>
            </form>
        </nav>
    </div>
</div>

@livewireScripts
</body>
</html>
