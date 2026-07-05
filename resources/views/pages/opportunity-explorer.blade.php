<x-layouts.app title="Opportunity Explorer">
    @php
        $activeFilters = collect($filters)->filter();
        $selected = fn ($key, $value) => ($filters[$key] ?? '') === $value ? 'selected' : '';
        $relatedTerms = function ($capability) {
            return str($capability->name.' '.$capability->slug.' '.$capability->description.' '.$capability->solutionPatterns->pluck('name')->join(' ').' '.$capability->solutionPatterns->pluck('slug')->join(' ').' '.$capability->matchedFrictionPoints->pluck('name')->join(' ').' '.$capability->matchedFrictionPoints->pluck('slug')->join(' '))->lower()->toString();
        };
    @endphp

    <section class="mx-auto max-w-6xl px-6 py-16">
        <p class="font-semibold text-cyan-300">Opportunity Explorer</p>
        <h1 class="mt-4 max-w-5xl text-4xl font-bold tracking-tight md:text-6xl">Navigate from business context to practical solution paths.</h1>
        <p class="mt-6 max-w-3xl text-lg text-slate-300">Use rules-based filters to connect industries, departments, workflows, friction points, solution patterns, and Garcia Systems capabilities. No AI, scoring, or black-box recommendations—just structured operating context.</p>
    </section>

    <section class="mx-auto grid max-w-6xl gap-8 px-6 lg:grid-cols-[18rem_1fr]">
        <aside class="rounded-3xl border border-white/10 bg-white/[0.03] p-5 shadow-2xl shadow-cyan-950/20 lg:sticky lg:top-6 lg:self-start">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-xl font-bold">Explore by context</h2>
                @if($activeFilters->isNotEmpty())
                    <a class="text-sm font-semibold text-cyan-300" href="{{ route('opportunity-explorer') }}">Reset</a>
                @endif
            </div>
            <form class="mt-5 space-y-4" method="GET" action="{{ route('opportunity-explorer') }}">
                <label class="block text-sm font-semibold text-slate-300">Keyword search
                    <input class="mt-2 w-full rounded-xl border border-white/10 bg-slate-950 px-3 py-2 text-slate-100" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="intake, healthcare, automation">
                </label>
                @foreach([
                    'industry' => ['Industry', $industries],
                    'company_type' => ['Company type', $companyTypes],
                    'department' => ['Department', $departments],
                    'workflow' => ['Workflow', $workflows],
                    'friction_point' => ['Friction point', $frictionPoints],
                    'capability' => ['Capability', $allCapabilities],
                ] as $name => [$label, $options])
                    <label class="block text-sm font-semibold text-slate-300">{{ $label }}
                        <select class="mt-2 w-full rounded-xl border border-white/10 bg-slate-950 px-3 py-2 text-slate-100" name="{{ $name }}" onchange="this.form.submit()">
                            <option value="">Any {{ strtolower($label) }}</option>
                            @foreach($options as $option)
                                <option value="{{ $option->slug }}" {{ $selected($name, $option->slug) }}>{{ $option->name }}</option>
                            @endforeach
                        </select>
                    </label>
                @endforeach
                <button class="w-full rounded-full bg-cyan-400 px-5 py-3 font-semibold text-slate-950">Update results</button>
            </form>
        </aside>

        <div>
            <div class="rounded-3xl border border-cyan-300/20 bg-cyan-300/10 p-5">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-200">Rules-based paths</p>
                <p class="mt-2 text-slate-300">{{ $capabilities->count() }} capability {{ Str::plural('match', $capabilities->count()) }} found. Follow the chain from industry to workflow friction, then into solution patterns, content, and services.</p>
            </div>

            @forelse($capabilities as $capability)
                @php
                    $patterns = $capability->solutionPatterns;
                    $frictions = $capability->matchedFrictionPoints;
                    $terms = $relatedTerms($capability);
                    $suggestedArticles = $articles->filter(fn ($article) => str($terms)->contains(str($article->title.' '.$article->excerpt.' '.$article->tags->pluck('name')->join(' '))->lower()->explode(' ')->filter()->take(8)->all()))->take(3);
                    if ($suggestedArticles->isEmpty()) $suggestedArticles = $articles->take(3);
                    $suggestedVideos = $videos->filter(fn ($video) => str($terms)->contains(str($video->title.' '.$video->description)->lower()->explode(' ')->filter()->take(8)->all()))->take(3);
                    if ($suggestedVideos->isEmpty()) $suggestedVideos = $videos->take(3);
                    $suggestedServices = collect($services)->filter(fn ($service) => str($terms)->contains($service['keywords']))->take(3);
                    if ($suggestedServices->isEmpty()) $suggestedServices = collect($services)->take(2);
                @endphp
                <article class="mt-6 overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04] shadow-2xl shadow-slate-950/30">
                    <div class="border-b border-white/10 bg-gradient-to-r from-cyan-400/15 to-blue-500/5 p-6">
                        <p class="text-sm font-semibold text-cyan-300">Capability</p>
                        <h2 class="mt-2 text-3xl font-bold">{{ $capability->name }}</h2>
                        <p class="mt-3 max-w-3xl text-slate-300">{{ $capability->description }}</p>
                    </div>
                    <div class="grid gap-5 p-6 md:grid-cols-2">
                        <div>
                            <h3 class="font-bold text-slate-100">Business path</h3>
                            <div class="mt-3 space-y-3">
                                @foreach($frictions as $friction)
                                    <div class="rounded-2xl border border-white/10 bg-slate-950/60 p-4">
                                        <p class="text-sm text-slate-400">{{ $friction->workflow?->industry?->name }} → {{ $friction->workflow?->department?->name }} → {{ $friction->workflow?->name }}</p>
                                        <p class="mt-1 font-semibold">{{ $friction->name }}</p>
                                        <p class="mt-1 text-sm text-slate-400">{{ $friction->impact }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="space-y-5">
                            <div><h3 class="font-bold">Related solution patterns</h3><div class="mt-2 flex flex-wrap gap-2">@foreach($patterns as $pattern)<span class="rounded-full bg-cyan-300/10 px-3 py-1 text-sm text-cyan-100">{{ $pattern->name }}</span>@endforeach</div></div>
                            <div><h3 class="font-bold">Suggested articles</h3><ul class="mt-2 space-y-2 text-sm text-slate-300">@foreach($suggestedArticles as $article)<li><a class="text-cyan-300" href="{{ route('articles.show', $article) }}">{{ $article->title }}</a> — {{ $article->excerpt }}</li>@endforeach</ul></div>
                            <div><h3 class="font-bold">Suggested videos</h3><ul class="mt-2 space-y-2 text-sm text-slate-300">@foreach($suggestedVideos as $video)<li><a class="text-cyan-300" href="{{ $video->url }}">{{ $video->title }}</a> — {{ $video->description }}</li>@endforeach</ul></div>
                            <div><h3 class="font-bold">Suggested services</h3><div class="mt-2 grid gap-2">@foreach($suggestedServices as $service)<div class="rounded-2xl border border-white/10 p-3"><p class="font-semibold">{{ $service['title'] }}</p><p class="text-sm text-slate-400">{{ $service['description'] }}</p></div>@endforeach</div></div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="mt-6 rounded-3xl border border-white/10 bg-white/[0.04] p-10 text-center">
                    <h2 class="text-2xl font-bold">No opportunity paths found</h2>
                    <p class="mt-3 text-slate-300">Try broadening the filters or clearing keyword search. The explorer only shows capabilities connected through existing Atlas relationships.</p>
                    <a class="mt-5 inline-flex rounded-full bg-cyan-400 px-5 py-3 font-semibold text-slate-950" href="{{ route('opportunity-explorer') }}">View all paths</a>
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.app>
