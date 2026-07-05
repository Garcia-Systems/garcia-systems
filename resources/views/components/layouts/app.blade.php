@props([
    'title' => null,
    'pageTitle' => null,
    'pageDescription' => null,
    'pageImage' => null,
    'canonicalUrl' => null,
    'ogType' => 'website',
    'structuredData' => [],
])

@php
    $siteName = 'Garcia Systems';
    $defaultDescription = 'Garcia Systems helps teams turn business problems into practical products, systems, automation, and AI-ready workflows.';
    $resolvedTitle = $pageTitle ?? $title ?? $siteName;
    $metaTitle = $resolvedTitle === $siteName ? $siteName : $resolvedTitle.' | '.$siteName;
    $metaDescription = str($pageDescription ?? $defaultDescription)->squish()->limit(160, '')->toString();
    $metaImage = $pageImage ?: asset('images/garcia-systems-og.png');
    $metaUrl = $canonicalUrl ?: url()->current();
    $jsonLd = collect([
        [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => url('/'),
            'description' => $defaultDescription,
        ],
        [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => url('/'),
        ],
    ])->merge(is_array($structuredData) && array_is_list($structuredData) ? $structuredData : [$structuredData])->filter()->values();
@endphp

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $metaTitle }}</title>
        <meta name="description" content="{{ $metaDescription }}">
        <link rel="canonical" href="{{ $metaUrl }}">
        <meta property="og:title" content="{{ $resolvedTitle }}">
        <meta property="og:description" content="{{ $metaDescription }}">
        <meta property="og:image" content="{{ $metaImage }}">
        <meta property="og:url" content="{{ $metaUrl }}">
        <meta property="og:type" content="{{ $ogType }}">
        <meta property="og:site_name" content="{{ $siteName }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $resolvedTitle }}">
        <meta name="twitter:description" content="{{ $metaDescription }}">
        <meta name="twitter:image" content="{{ $metaImage }}">
        @foreach ($jsonLd as $schema)
            <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endforeach
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
