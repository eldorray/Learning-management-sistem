@php
    $content = \App\Support\LandingPageContent::values();
@endphp
<!DOCTYPE html>
<html lang="{{ $content['language'] }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $content['text_001'] }}</title>
    <meta name="description" content="{{ $content['description'] }}">
    <meta name="theme-color" content="#05070a">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="{{ $content['favicon'] ?: '/icons/icon-192x192.png' }}">
    <link rel="apple-touch-icon" href="{{ $content['favicon'] ?: '/icons/icon-192x192.png' }}">
    @vite('resources/js/landing.tsx')
</head>
<body>
    <div id="kage-root"></div>
    <nav class="lms-access" aria-label="LMS">
        <a href="{{ route('login') }}">{{ $content['login_label'] }}</a>
        <a href="{{ route('register') }}">{{ $content['register_label'] }}</a>
    </nav>
    <div id="pwa-install-banner" class="hidden">
        <button id="pwa-install" type="button">{{ $content['install_label'] }}</button>
        <button id="pwa-later" type="button">{{ $content['install_later'] }}</button>
    </div>
</body>
</html>
