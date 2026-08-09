<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Garcia Systems' }}</title>
        @if (file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="antialiased">
        <x-header />
        <main id="main-content">{{ $slot ?? '' }}@yield('content')</main>
        <x-footer />
    </body>
</html>
