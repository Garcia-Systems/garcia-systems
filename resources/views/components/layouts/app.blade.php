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
    <body class="bg-slate-950 text-slate-100">
        <header class="border-b border-white/10 bg-slate-950/90">
            <nav class="mx-auto flex max-w-6xl items-center justify-between p-5">
                <a href="{{ route('home') }}" class="text-xl font-bold">Garcia Systems</a>
                <div class="flex flex-wrap gap-4 text-sm text-slate-300">
                    <a href="{{ route('services') }}">Services</a>
                    <a href="{{ route('atlas') }}">Atlas</a>
                    <a href="{{ route('opportunity-explorer') }}">Explorer</a>
                    <a href="{{ route('assessment') }}">Assessment</a>
                    <a href="{{ route('articles.index') }}">Articles</a>
                    <a href="{{ route('videos') }}">Videos</a>
                    <a href="{{ route('tools') }}">Tools</a>
                    <a href="{{ route('contact') }}">Contact</a>
                </div>
            </nav>
        </header>

        <main>{{ $slot }}</main>

        <footer class="mt-16 border-t border-white/10">
            <div class="mx-auto max-w-6xl p-6 text-sm text-slate-400">
                © {{ date('Y') }} Garcia Systems. Practical systems, automation, and AI readiness consulting.
            </div>
        </footer>
    </body>
</html>
