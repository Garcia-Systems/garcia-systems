@php
    $routeFor = function ($item) {
        return match (get_class($item)) {
            App\Models\Industry::class => route('atlas.industries.show', $item->slug),
            App\Models\CompanyType::class => route('atlas.company-types.show', $item->slug),
            App\Models\Department::class => route('atlas.departments.show', $item->slug),
            App\Models\Workflow::class => route('atlas.workflows.show', $item->slug),
            App\Models\FrictionPoint::class => route('atlas.friction-points.show', $item->slug),
            App\Models\SolutionPattern::class => route('atlas.solution-patterns.show', $item->slug),
            App\Models\Capability::class => route('atlas.capabilities.show', $item->slug),
            default => '#',
        };
    };
@endphp

<x-layouts.app title="{{ $record->name }} | Opportunity Atlas">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-cyan-200">Home</a>
            <span>/</span>
            <a href="{{ route('atlas') }}" class="hover:text-cyan-200">Opportunity Atlas</a>
            <span>/</span>
            <span class="text-slate-200">{{ $record->name }}</span>
        </nav>

        <div class="mt-8 rounded-3xl border border-white/10 bg-white/5 p-8">
            <p class="font-semibold text-cyan-300">{{ $type }}</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight md:text-6xl">{{ $record->name }}</h1>
            <p class="mt-5 max-w-3xl text-lg text-slate-300">{{ $record->description ?: 'No description has been added for this atlas record yet.' }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('contact') }}" class="rounded-full bg-cyan-400 px-5 py-2 font-semibold text-slate-950">Discuss this opportunity</a>
                <a href="{{ route('atlas') }}" class="rounded-full border border-white/15 px-5 py-2 font-semibold text-slate-100">Back to Atlas</a>
            </div>
        </div>
    </section>

    <section class="mx-auto grid max-w-6xl gap-6 px-6 pb-20 lg:grid-cols-2">
        @foreach([
            'Related parent records' => $parents,
            'Related child records' => $children,
            'Related workflows' => $workflows,
            'Related friction points' => $frictionPoints,
            'Related solution patterns' => $solutionPatterns,
            'Related capabilities' => $capabilities,
        ] as $heading => $items)
            <x-card>
                <h2 class="text-2xl font-bold">{{ $heading }}</h2>
                <div class="mt-4 space-y-3">
                    @forelse($items as $item)
                        <a href="{{ $routeFor($item) }}" class="block rounded-2xl border border-white/10 bg-slate-950/60 p-4 hover:border-cyan-300/50">
                            <span class="font-semibold text-cyan-200">{{ $item->name }}</span>
                            @if($item->description)
                                <p class="mt-1 text-sm text-slate-300">{{ $item->description }}</p>
                            @endif
                        </a>
                    @empty
                        <p class="rounded-2xl border border-dashed border-white/10 p-4 text-sm text-slate-400">No related {{ str($heading)->after('Related ')->lower() }} are available yet.</p>
                    @endforelse
                </div>
            </x-card>
        @endforeach

        <x-card>
            <h2 class="text-2xl font-bold">Related articles</h2>
            <div class="mt-4 space-y-3">
                @forelse($articles as $article)
                    <a href="{{ route('articles.show', $article->slug) }}" class="block rounded-2xl border border-white/10 bg-slate-950/60 p-4 hover:border-cyan-300/50">
                        <span class="font-semibold text-cyan-200">{{ $article->title }}</span>
                        <p class="mt-1 text-sm text-slate-300">{{ $article->excerpt }}</p>
                    </a>
                @empty
                    <p class="rounded-2xl border border-dashed border-white/10 p-4 text-sm text-slate-400">No related articles are published yet.</p>
                @endforelse
            </div>
        </x-card>

        <x-card>
            <h2 class="text-2xl font-bold">Related videos</h2>
            <div class="mt-4 space-y-3">
                @forelse($videos as $video)
                    <a href="{{ $video->url }}" class="block rounded-2xl border border-white/10 bg-slate-950/60 p-4 hover:border-cyan-300/50">
                        <span class="font-semibold text-cyan-200">{{ $video->title }}</span>
                        <p class="mt-1 text-sm text-slate-300">{{ $video->description }}</p>
                    </a>
                @empty
                    <p class="rounded-2xl border border-dashed border-white/10 p-4 text-sm text-slate-400">No related videos are published yet.</p>
                @endforelse
            </div>
        </x-card>
    </section>
</x-layouts.app>
