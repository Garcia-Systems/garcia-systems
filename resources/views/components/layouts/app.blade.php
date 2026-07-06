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
        <x-header />

        <main>{{ $slot }}</main>

        <x-footer />
    </body>
</html>
