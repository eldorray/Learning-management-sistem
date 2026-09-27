@php
    $content = \App\Support\SketchbookContent::values();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $content['title'] }}</title>
    <meta name="description" content="{{ $content['description'] }}">
    <meta name="theme-color" content="#ece7dc">
    <link rel="manifest" href="/manifest.json">
    @php($favicon = \App\Models\Setting::get('app_favicon'))
    <link rel="icon" href="{{ $favicon ? asset('storage/'.$favicon) : '/icons/icon-192x192.png' }}">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    @vite('resources/js/landing.tsx')
</head>
<body>
    <div id="landing-root"></div>
    <div id="pwa-install-banner" class="hidden">
        <button id="pwa-install" type="button">{{ $content['install_label'] }}</button>
        <button id="pwa-later" type="button">{{ $content['install_later'] }}</button>
    </div>
</body>
</html>
