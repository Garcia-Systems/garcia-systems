@php
    $description = str($article->seo_description ?: $article->excerpt ?: $article->body)->stripTags()->squish()->limit(160, '')->toString();
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $article->seo_title ?: $article->title,
        'description' => $description,
        'image' => $article->preview_image_url,
        'datePublished' => optional($article->published_at)->toAtomString(),
        'dateModified' => $article->updated_at->toAtomString(),
        'author' => ['@type' => 'Organization', 'name' => $article->author_name],
        'publisher' => ['@type' => 'Organization', 'name' => 'Garcia Systems'],
        'mainEntityOfPage' => route('articles.show', $article),
    ];
@endphp

<x-layouts.app
    :page-title="$article->seo_title ?: $article->title"
    :page-description="$description"
    :page-image="$article->preview_image_url"
    :canonical-url="route('articles.show', $article)"
    og-type="article"
    :structured-data="$schema"
>
    <article class="mx-auto max-w-4xl px-6 py-16">
        <div class="mx-auto max-w-3xl text-center">
            @if($article->category)
                <p class="inline-flex rounded-full bg-cyan-400/10 px-4 py-2 text-sm font-semibold text-cyan-300">{{ $article->category->name }}</p>
            @endif
            <h1 class="mt-6 text-4xl font-bold tracking-tight md:text-6xl">{{ $article->title }}</h1>
            <div class="mt-6 flex flex-wrap justify-center gap-x-4 gap-y-2 text-sm text-slate-400">
                <span>By {{ $article->author_name }}</span>
                @if($article->published_at)
                    <time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->format('F j, Y') }}</time>
                @endif
                <span>{{ $article->reading_time }} min read</span>
            </div>
            <p class="mt-8 rounded-3xl border border-white/10 bg-white/5 p-6 text-left text-xl leading-8 text-slate-200 md:text-center">{{ $article->excerpt }}</p>
        </div>

        @if($article->is_external)
            <section class="mt-12 overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-2xl shadow-slate-950/40">
                @if($article->preview_image_url)
                    <img class="aspect-[16/9] w-full object-cover" src="{{ $article->preview_image_url }}" alt="Preview image for {{ $article->title }}">
                @else
                    <div class="flex aspect-[16/9] w-full items-center justify-center bg-gradient-to-br from-slate-800 via-slate-900 to-cyan-950 text-sm font-semibold uppercase tracking-[0.25em] text-cyan-200">Garcia Systems Article Preview</div>
                @endif
                <div class="p-6 md:p-8">
                    <div class="flex flex-wrap items-center gap-2 text-xs font-medium uppercase tracking-wide text-slate-400">
                        <span class="rounded-full bg-white/10 px-3 py-1 text-slate-200">Substack</span>
                        @if($article->category)<span class="rounded-full bg-cyan-400/10 px-3 py-1 text-cyan-300">{{ $article->category->name }}</span>@endif
                        @if($article->published_at)<time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->format('F j, Y') }}</time>@endif
                    </div>
                    <h2 class="mt-5 text-3xl font-bold tracking-tight">{{ $article->title }}</h2>
                    <p class="mt-4 text-lg leading-8 text-slate-200">{{ $article->excerpt }}</p>
                    @if($article->tags->count())
                        <div class="mt-5 flex flex-wrap gap-2">@foreach($article->tags as $tag)<span class="rounded-full border border-white/10 px-3 py-1 text-sm text-slate-300">{{ $tag->name }}</span>@endforeach</div>
                    @endif
                    <p class="mt-6 text-sm text-slate-400">The full article is published on Substack. Garcia Systems keeps this preview here so you can decide before opening the external post.</p>
                    <a class="mt-6 inline-flex rounded-2xl bg-cyan-400 px-6 py-3 font-semibold text-slate-950 hover:bg-cyan-300 focus:outline-none focus:ring-2 focus:ring-cyan-200" href="{{ $article->external_url }}" target="_blank" rel="noopener noreferrer">Read the full article on Substack →</a>
                </div>
            </section>
        @elseif($article->featured_image_url)
            <img class="mt-10 h-[22rem] w-full rounded-3xl border border-white/10 object-cover shadow-2xl shadow-slate-950/50" src="{{ $article->featured_image_url }}" alt="Featured image for {{ $article->title }}">
        @endif

        @if(! $article->is_external && $article->body)
            <div class="prose prose-invert prose-lg mt-12 max-w-none prose-headings:tracking-tight prose-a:text-cyan-300 prose-strong:text-white whitespace-pre-line text-slate-200">{{ $article->body }}</div>
        @endif
    </article>

    <section class="mx-auto max-w-6xl px-6 py-8">
        <div class="rounded-3xl border border-cyan-300/20 bg-cyan-300/10 p-8 md:flex md:items-center md:justify-between md:gap-8">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-300">Next step</p>
                <h2 class="mt-3 text-3xl font-bold">Need help implementing ideas like these?</h2>
                <p class="mt-3 max-w-2xl text-slate-300">Garcia Systems helps teams assess opportunities, clarify workflows, and turn operational friction into practical products, systems, and AI-ready improvements.</p>
            </div>
            <div class="mt-6 flex flex-wrap gap-3 md:mt-0 md:justify-end">
                <a class="rounded-2xl bg-cyan-400 px-5 py-3 font-semibold text-slate-950" href="{{ route('atlas') }}">Explore the Opportunity Atlas</a>
                <a class="rounded-2xl border border-white/15 px-5 py-3 font-semibold text-white" href="{{ route('assessment') }}">Book an AI Readiness Assessment</a>
                <a class="rounded-2xl border border-white/15 px-5 py-3 font-semibold text-white" href="{{ route('contact') }}">Contact Garcia Systems</a>
            </div>
        </div>
    </section>

    @if($relatedArticles->count())
        <section class="mx-auto max-w-6xl px-6 py-16">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-300">Related content</p>
                    <h2 class="mt-3 text-3xl font-bold">Continue reading</h2>
                </div>
                <a class="hidden text-sm font-semibold text-cyan-300 md:inline" href="{{ route('articles.index') }}">All articles →</a>
            </div>
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                @foreach($relatedArticles as $related)
                    <x-card class="overflow-hidden p-0">
                        @if($related->featured_image_url)
                            <img class="h-40 w-full object-cover" src="{{ $related->featured_image_url }}" alt="Featured image for {{ $related->title }}">
                        @endif
                        <div class="p-6">
                            <p class="text-sm text-cyan-300">{{ $related->category?->name }}</p>
                            <h3 class="mt-2 text-lg font-semibold">{{ $related->title }}</h3>
                            <p class="mt-3 text-sm text-slate-300">{{ $related->excerpt }}</p>
                            <a class="mt-4 inline-block text-sm font-semibold text-cyan-300" href="{{ route('articles.show', $related) }}">Read next →</a>
                        </div>
                    </x-card>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.app>
