<x-layouts.app title="Articles">
    <section class="mx-auto max-w-6xl px-6 py-16">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-300">Insights</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight md:text-5xl">Articles</h1>
            <p class="mt-5 text-lg text-slate-300">Practical thinking on automation, workflow modernization, AI readiness, and systems strategy for teams turning business problems into durable operating improvements.</p>
        </div>

        <form method="get" action="{{ route('articles.index') }}" class="mt-10 grid gap-4 rounded-3xl border border-white/10 bg-white/5 p-5 md:grid-cols-[1fr_220px_auto]">
            <label class="grid gap-2 text-sm font-medium text-slate-200">
                Search articles
                <input class="rounded-2xl border border-white/10 bg-slate-950 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-cyan-300 focus:outline-none" name="q" value="{{ $search }}" placeholder="automation, reporting, workflow...">
            </label>
            <label class="grid gap-2 text-sm font-medium text-slate-200">
                Category
                <select class="rounded-2xl border border-white/10 bg-slate-950 px-4 py-3 text-slate-100 focus:border-cyan-300 focus:outline-none" name="category">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug || $selectedCategory === $category->name)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <div class="flex items-end gap-3">
                <button class="rounded-2xl bg-cyan-400 px-5 py-3 font-semibold text-slate-950 hover:bg-cyan-300">Filter</button>
                @if($search || $selectedCategory)
                    <a class="rounded-2xl border border-white/10 px-5 py-3 text-sm text-slate-300 hover:text-white" href="{{ route('articles.index') }}">Clear</a>
                @endif
            </div>
        </form>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 text-sm text-slate-400">
            <p>{{ number_format($resultCount) }} {{ Str::plural('article', $resultCount) }} found</p>
            @if($search || $selectedCategory)
                <p>Showing results @if($search) for “{{ $search }}” @endif @if($selectedCategory) in {{ $categories->firstWhere('slug', $selectedCategory)?->name ?? $selectedCategory }} @endif</p>
            @endif
        </div>

        @if($articles->count())
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                @foreach($articles as $article)
                    <x-card class="overflow-hidden p-0">
                        @if($article->featured_image_url)
                            <img class="h-48 w-full object-cover" src="{{ $article->featured_image_url }}" alt="Featured image for {{ $article->title }}">
                        @endif
                        <div class="p-6">
                            <div class="flex flex-wrap items-center gap-2 text-xs font-medium uppercase tracking-wide text-slate-400">
                                @if($article->category)<span class="rounded-full bg-cyan-400/10 px-3 py-1 text-cyan-300">{{ $article->category->name }}</span>@endif
                                @if($article->published_at)<time datetime="{{ $article->published_at->toDateString() }}">{{ $article->published_at->format('M j, Y') }}</time>@endif
                            </div>
                            <h2 class="mt-4 text-xl font-semibold leading-tight">{{ $article->title }}</h2>
                            <p class="mt-3 text-sm text-slate-400">By {{ $article->author_name }}</p>
                            <p class="mt-3 text-slate-300">{{ $article->excerpt }}</p>
                            <a class="mt-5 inline-block font-semibold text-cyan-300" href="{{ route('articles.show',$article) }}">Read article →</a>
                        </div>
                    </x-card>
                @endforeach
            </div>
            <div class="mt-8">{{ $articles->links() }}</div>
        @else
            <div class="mt-10 rounded-3xl border border-dashed border-white/15 bg-white/5 p-10 text-center">
                <h2 class="text-2xl font-bold">No articles match those filters.</h2>
                <p class="mt-3 text-slate-300">Try a broader search term or clear the category filter to browse all published insights.</p>
                <a class="mt-6 inline-block rounded-2xl bg-cyan-400 px-5 py-3 font-semibold text-slate-950" href="{{ route('articles.index') }}">View all articles</a>
            </div>
        @endif
    </section>
</x-layouts.app>
