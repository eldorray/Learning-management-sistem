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
<body class="min-h-screen bg-[#f5f7f9] text-[#2c2f31] flex items-center justify-center p-4">

    <!-- Background decorations -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-[#6c9fff]/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-[#73f2dd]/10 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-md relative">
        <!-- Logo -->
        <div class="text-center mb-8">
            @if(\App\Models\Setting::get('app_logo'))
            <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}"
                 class="w-16 h-16 rounded-2xl object-contain mx-auto mb-4 shadow-lg shadow-blue-500/20"
                 alt="Logo">
            @else
            <div class="w-16 h-16 bg-[#0058ba] rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-500/20">
                <span class="material-symbols-outlined text-[#f0f2ff] text-3xl">school</span>
            </div>
            @endif
            <h1 class="text-2xl font-headline font-extrabold text-[#2c2f31]">{{ \App\Models\Setting::get('app_name', 'LMS Arrahmah') }}</h1>
            <p class="text-[#595c5e] text-sm mt-1">{{ \App\Models\Setting::get('app_tagline', 'Platform Pembelajaran Digital') }}</p>
        </div>

        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
