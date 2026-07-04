<x-layouts.app title="Opportunity Atlas">
    <section class="mx-auto max-w-6xl px-6 py-16">
        <p class="font-semibold text-cyan-300">Opportunity Atlas</p>
        <h1 class="mt-4 text-4xl font-bold tracking-tight md:text-6xl">Explore operational friction by industry, workflow, capability, and solution pattern.</h1>
        <p class="mt-5 max-w-3xl text-lg text-slate-300">Use the filters to combine discovery lenses and find practical places where better systems, automation, or product execution could help.</p>

        <div class="mt-8 grid gap-3 md:grid-cols-7">
            @foreach($summary as $label => $count)
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <div class="text-2xl font-bold text-cyan-200">{{ $count }}</div>
                    <div class="mt-1 text-xs uppercase tracking-wide text-slate-400">{{ str($label)->replace('_', ' ') }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 pb-10">
        <form method="GET" action="{{ route('atlas') }}" class="rounded-3xl border border-white/10 bg-white/5 p-5">
            <div class="grid gap-4 md:grid-cols-4">
                @foreach([
                    'industry' => 'Industry',
                    'company_type' => 'Company Type',
                    'department' => 'Department',
                    'workflow' => 'Workflow',
                    'friction_point' => 'Friction Point',
                    'capability' => 'Capability',
                    'solution_pattern' => 'Solution Pattern',
                ] as $key => $label)
                    <label class="text-sm font-semibold text-slate-200">
                        {{ $label }}
                        <select name="{{ $key }}" class="mt-2 w-full rounded-xl border border-white/10 bg-slate-950 px-3 py-2 text-slate-100">
                            <option value="">All {{ strtolower($label) }}</option>
                            @foreach($filterOptions[$key] as $option)
                                <option value="{{ $option->slug }}" @selected(($filters[$key] ?? '') === $option->slug)>{{ $option->name }}</option>
                            @endforeach
                        </select>
                    </label>
                @endforeach
            </div>
            <div class="mt-5 flex flex-wrap items-center gap-3">
                <button class="rounded-full bg-cyan-400 px-5 py-2 font-semibold text-slate-950">Apply filters</button>
                <a href="{{ route('atlas') }}" class="rounded-full border border-white/15 px-5 py-2 font-semibold text-slate-100">Clear filters</a>
                @forelse($filters as $key => $value)
                    <span class="rounded-full bg-cyan-400/10 px-3 py-1 text-sm text-cyan-200">{{ str($key)->replace('_', ' ')->title() }}: {{ str($value)->replace('-', ' ')->title() }}</span>
                @empty
                    <span class="text-sm text-slate-400">No filters active. Showing all atlas examples.</span>
                @endforelse
            </div>
        </form>
    </section>

    <section class="mx-auto max-w-6xl px-6 pb-20">
        @if($workflows->isEmpty())
            <x-card class="text-center">
                <h2 class="text-2xl font-bold">No atlas results yet</h2>
                <p class="mt-3 text-slate-300">Try removing one filter or exploring a broader industry, department, or capability.</p>
            </x-card>
        @else
            <div class="grid gap-6">
                @foreach($workflows as $workflow)
                    <x-card>
                        <div class="flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-wide">
                            <span class="rounded-full bg-white/10 px-3 py-1">Industry: {{ $workflow->industry?->name ?? 'General' }}</span>
                            <span class="rounded-full bg-white/10 px-3 py-1">Company Type: {{ $workflow->companyType?->name ?? 'Any' }}</span>
                            <span class="rounded-full bg-white/10 px-3 py-1">Department: {{ $workflow->department?->name ?? 'Cross-functional' }}</span>
                        </div>
                        <h2 class="mt-4 text-2xl font-bold">{{ $workflow->name }}</h2>
                        <p class="mt-2 text-slate-300">{{ $workflow->description }}</p>

                        <div class="mt-5 rounded-2xl border border-cyan-300/20 bg-cyan-300/5 p-4 text-sm text-slate-300">
                            <strong class="text-cyan-200">Hierarchy:</strong>
                            {{ $workflow->industry?->name ?? 'Industry' }} ↓ {{ $workflow->companyType?->name ?? 'Company Type' }} ↓ {{ $workflow->department?->name ?? 'Department' }} ↓ {{ $workflow->name }} ↓ Friction Point ↓ Solution Pattern ↓ Capability ↓ Articles ↓ Videos ↓ Services
                        </div>

                        <div class="mt-5 grid gap-4 md:grid-cols-{{ max(1, min(2, $workflow->frictionPoints->count())) }}">
                            @foreach($workflow->frictionPoints as $friction)
                                <div class="rounded-2xl border border-white/10 bg-slate-950/60 p-4">
                                    <div class="text-sm font-semibold text-rose-200">Friction: {{ $friction->name }}</div>
                                    <p class="mt-2 text-sm text-slate-300">{{ $friction->description }}</p>
                                    @if($friction->impact)<p class="mt-2 text-sm text-slate-400">Impact: {{ $friction->impact }}</p>@endif

                                    @foreach($friction->solutionPatterns as $pattern)
                                        <div class="mt-4 border-t border-white/10 pt-4">
                                            <div class="font-semibold text-cyan-200">Solution Pattern: {{ $pattern->name }}</div>
                                            <p class="mt-1 text-sm text-slate-300">{{ $pattern->description }}</p>
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                @foreach($pattern->capabilities as $capability)
                                                    <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-200">Capability: {{ $capability->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-5 grid gap-3 text-sm md:grid-cols-3">
                            <div class="rounded-2xl bg-white/5 p-4"><strong>Associated Articles ({{ $articles->count() }})</strong><p class="mt-2 text-slate-300">{{ $articles->pluck('title')->take(2)->join(', ') ?: 'None published yet' }}</p></div>
                            <div class="rounded-2xl bg-white/5 p-4"><strong>Associated Videos ({{ $videos->count() }})</strong><p class="mt-2 text-slate-300">{{ $videos->pluck('title')->take(2)->join(', ') ?: 'None published yet' }}</p></div>
                            <div class="rounded-2xl bg-white/5 p-4"><strong>Associated Services ({{ $services->count() }})</strong><p class="mt-2 text-slate-300">{{ $services->take(2)->join(', ') }}</p></div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>
