@php
    $description = str($article->seo_description ?: $article->excerpt ?: $article->body)->stripTags()->squish()->limit(160, '')->toString();
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $article->seo_title ?: $article->title,
        'description' => $description,
        'image' => $article->featured_image_url,
        'datePublished' => optional($article->published_at)->toAtomString(),
        'dateModified' => $article->updated_at->toAtomString(),
        'author' => ['@type' => 'Organization', 'name' => 'Garcia Systems'],
        'publisher' => ['@type' => 'Organization', 'name' => 'Garcia Systems'],
        'mainEntityOfPage' => route('articles.show', $article),
    ];
@endphp

<x-layouts.app
    :page-title="$article->seo_title ?: $article->title"
    :page-description="$description"
    :page-image="$article->featured_image_url"
    :canonical-url="route('articles.show', $article)"
    og-type="article"
    :structured-data="$schema"
>
    <article class="mx-auto max-w-3xl px-6 py-16"><p class="text-cyan-300">{{ $article->category?->name }}</p><h1 class="mt-3 text-4xl font-bold">{{ $article->title }}</h1><p class="mt-5 text-xl text-slate-300">{{ $article->excerpt }}</p><div class="prose prose-invert mt-8 max-w-none whitespace-pre-line text-slate-200">{{ $article->body }}</div></article>
</x-layouts.app>
