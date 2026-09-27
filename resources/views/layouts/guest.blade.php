<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }} | {{ \App\Models\Setting::get('app_name', 'LMS Arrahmah') }}</title>
    @if(\App\Models\Setting::get('app_favicon'))
    <link rel="icon" href="{{ asset('storage/' . \App\Models\Setting::get('app_favicon')) }}" type="image/png">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-[#ece7dc] text-[#2b2721] flex items-center justify-center p-4">

    <!-- The painted paper ground and botanicals of the public sketchbook -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute inset-0 bg-[url('/landing-pages/meng-to-sketchbook/bg-wash.jpg')] bg-cover bg-top opacity-85"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-[#ece7dc]/25 via-[#ece7dc]/70 to-[#ece7dc]"></div>
        <img src="/landing-pages/meng-to-sketchbook/botany-left.png" alt="" class="hidden md:block absolute left-0 bottom-[2%] w-[clamp(120px,15vw,250px)] opacity-50">
        <img src="/landing-pages/meng-to-sketchbook/botany-right.png" alt="" class="hidden md:block absolute right-0 -bottom-[2%] w-[clamp(100px,12vw,200px)] opacity-50">
    </div>

    <div class="w-full max-w-md relative">
        <!-- Logo -->
        <div class="text-center mb-8">
            @if(\App\Models\Setting::get('app_logo'))
            <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}"
                 class="w-16 h-16 rounded-2xl object-contain mx-auto mb-4 shadow-lg shadow-blue-500/20"
                 alt="Logo">
            @else
            <div class="w-16 h-16 bg-[#8a5a31] rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-500/20">
                <span class="material-symbols-outlined text-[#fbf6ee] text-3xl">school</span>
            </div>
            @endif
            <h1 class="text-2xl font-headline font-extrabold text-[#2b2721]">{{ \App\Models\Setting::get('app_name', 'LMS Arrahmah') }}</h1>
            <p class="text-[#6b6358] text-sm mt-1">{{ \App\Models\Setting::get('app_tagline', 'Platform Pembelajaran Digital') }}</p>
        </div>

        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
