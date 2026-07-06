<x-layouts.app title="Opportunity Atlas" page-description="Explore operational friction by industry, workflow, capability, and solution pattern before choosing a technology path.">
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
            <label class="block text-sm font-semibold text-slate-200">
                Keyword search
                <input name="q" value="{{ $keyword }}" placeholder="Search workflows, friction, patterns, or capabilities" class="mt-2 w-full rounded-xl border border-white/10 bg-slate-950 px-3 py-2 text-slate-100">
            </label>
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
                                @php
                                    $optionLabel = in_array($key, ['workflow', 'friction_point', 'solution_pattern'], true)
                                        ? str($label)->headline().' option '.$loop->iteration
                                        : $option->name;
                                @endphp
                                <option value="{{ $option->slug }}" @selected(($filters[$key] ?? '') === $option->slug)>{{ $optionLabel }}</option>
                            @endforeach
                        </select>
                    </label>
                @endforeach
            </div>
            <div class="mt-5 flex flex-wrap items-center gap-3">
                <button class="rounded-full bg-cyan-400 px-5 py-2 font-semibold text-slate-950">Apply filters</button>
                <a href="{{ route('atlas') }}" class="rounded-full border border-white/15 px-5 py-2 font-semibold text-slate-100">Clear filters</a>
                @if($keyword !== '')
                    <span class="rounded-full bg-cyan-400/10 px-3 py-1 text-sm text-cyan-200">Search: {{ $keyword }}</span>
                @endif
                @forelse($filters as $key => $value)
                    <span class="rounded-full bg-cyan-400/10 px-3 py-1 text-sm text-cyan-200">{{ str($key)->replace('_', ' ')->title() }}: {{ str($value)->replace('-', ' ')->title() }}</span>
                @empty
                    @if($keyword === '')
                        <span class="text-sm text-slate-400">No filters active. Showing all atlas examples.</span>
                    @endif
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
                            <span class="rounded-full bg-white/10 px-3 py-1">Industry: @if($workflow->industry)<a class="hover:text-cyan-200" href="{{ route('atlas.industries.show', $workflow->industry->slug) }}">{{ $workflow->industry->name }}</a>@else General @endif</span>
                            <span class="rounded-full bg-white/10 px-3 py-1">Company Type: @if($workflow->companyType)<a class="hover:text-cyan-200" href="{{ route('atlas.company-types.show', $workflow->companyType->slug) }}">{{ $workflow->companyType->name }}</a>@else Any @endif</span>
                            <span class="rounded-full bg-white/10 px-3 py-1">Department: @if($workflow->department)<a class="hover:text-cyan-200" href="{{ route('atlas.departments.show', $workflow->department->slug) }}">{{ $workflow->department->name }}</a>@else Cross-functional @endif</span>
                        </div>
                        <h2 class="mt-4 text-2xl font-bold"><a href="{{ route('atlas.workflows.show', $workflow->slug) }}" class="hover:text-cyan-200">{{ $workflow->name }}</a></h2>
                        <p class="mt-2 text-slate-300">{{ $workflow->description }}</p>

                        <div class="mt-5 rounded-2xl border border-cyan-300/20 bg-cyan-300/5 p-4 text-sm text-slate-300">
                            <strong class="text-cyan-200">Hierarchy:</strong>
                            {{ $workflow->industry?->name ?? 'Industry' }} ↓ {{ $workflow->companyType?->name ?? 'Company Type' }} ↓ {{ $workflow->department?->name ?? 'Department' }} ↓ {{ $workflow->name }} ↓ Friction Point ↓ Solution Pattern ↓ Capability ↓ Articles ↓ Videos ↓ Services
                        </div>

                        <div class="mt-5 grid gap-4 md:grid-cols-{{ max(1, min(2, $workflow->frictionPoints->count())) }}">
                            @foreach($workflow->frictionPoints as $friction)
                                <div class="rounded-2xl border border-white/10 bg-slate-950/60 p-4">
                                    <div class="text-sm font-semibold text-rose-200">Friction: <a href="{{ route('atlas.friction-points.show', $friction->slug) }}" class="hover:text-rose-100">{{ $friction->name }}</a></div>
                                    <p class="mt-2 text-sm text-slate-300">{{ $friction->description }}</p>
                                    @if($friction->impact)<p class="mt-2 text-sm text-slate-400">Impact: {{ $friction->impact }}</p>@endif

                                    @foreach($friction->solutionPatterns as $pattern)
                                        <div class="mt-4 border-t border-white/10 pt-4">
                                            <div class="font-semibold text-cyan-200">Solution Pattern: <a href="{{ route('atlas.solution-patterns.show', $pattern->slug) }}" class="hover:text-cyan-100">{{ $pattern->name }}</a></div>
                                            <p class="mt-1 text-sm text-slate-300">{{ $pattern->description }}</p>
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                @foreach($pattern->capabilities as $capability)
                                                    <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-200">Capability: <a href="{{ route('atlas.capabilities.show', $capability->slug) }}" class="hover:text-emerald-100">{{ $capability->name }}</a></span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-5 grid gap-3 text-sm md:grid-cols-3">
                            <div class="rounded-2xl bg-white/5 p-4"><strong>Related Articles ({{ $workflow->articles_count }})</strong><p class="mt-2 text-slate-300">{{ $workflow->articles->pluck('title')->take(2)->join(', ') ?: 'None connected yet' }}</p></div>
                            <div class="rounded-2xl bg-white/5 p-4"><strong>Related Videos ({{ $workflow->videos_count }})</strong><p class="mt-2 text-slate-300">{{ $workflow->videos->pluck('title')->take(2)->join(', ') ?: 'None connected yet' }}</p></div>
                            <div class="rounded-2xl bg-white/5 p-4"><strong>Related Services ({{ $workflow->services_count }})</strong><p class="mt-2 text-slate-300">{{ $workflow->services->pluck('name')->take(2)->join(', ') ?: 'None connected yet' }}</p></div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>
