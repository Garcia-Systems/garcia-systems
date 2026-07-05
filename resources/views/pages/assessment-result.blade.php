<x-layouts.app title="Assessment Result" page-description="Your AI readiness score, category breakdown, recommendations, and practical next steps.">
    <section class="mx-auto max-w-4xl px-6 py-16">
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-cyan-300">AI readiness assessment</p>
        <h1 class="mt-3 text-4xl font-bold">Your readiness result</h1>

        <x-card class="mt-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-cyan-300">Overall score: {{ $assessment->score }}</p>
                    <h2 class="mt-2 text-3xl font-semibold">{{ $assessment->result_tier }}</h2>
                    <p class="mt-4 text-slate-300">{{ $assessment->summary }}</p>
                </div>
                <a class="rounded bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950" href="{{ route('contact') }}">Discuss next steps</a>
            </div>
        </x-card>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <x-card>
                <h2 class="text-2xl font-semibold">Category breakdown</h2>
                <div class="mt-5 space-y-4">
                    @foreach($categoryScores as $category)
                        <div>
                            <div class="flex items-center justify-between gap-4 text-sm">
                                <p class="font-semibold text-white">{{ $category['label'] }}</p>
                                <p class="text-cyan-300">{{ $category['score'] }} / {{ $category['max_score'] }}</p>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-white/10">
                                <div class="h-full rounded-full bg-cyan-400" style="width: {{ ($category['score'] / $category['max_score']) * 100 }}%"></div>
                            </div>
                            @if(! empty($category['question']))
                                <p class="mt-2 text-sm text-slate-400">{{ $category['question'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-card>

            <x-card>
                <h2 class="text-2xl font-semibold">Recommendations</h2>
                <ul class="mt-5 space-y-3 text-slate-300">
                    @foreach(($assessment->recommendations ?: $result['recommendations']) as $recommendation)
                        <li class="rounded border border-slate-700 bg-slate-950/60 p-3">{{ $recommendation }}</li>
                    @endforeach
                </ul>
            </x-card>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <x-card>
                <h2 class="text-2xl font-semibold">Risks to watch</h2>
                <ul class="mt-4 list-disc space-y-2 pl-5 text-slate-300">
                    @foreach(($assessment->risks ?: $result['risks']) as $risk)
                        <li>{{ $risk }}</li>
                    @endforeach
                </ul>
            </x-card>

            <x-card>
                <h2 class="text-2xl font-semibold">Recommended next steps</h2>
                <ul class="mt-4 list-disc space-y-2 pl-5 text-slate-300">
                    @foreach(($assessment->next_steps ?: $result['next_steps']) as $step)
                        <li>{{ $step }}</li>
                    @endforeach
                </ul>
            </x-card>
        </div>

        <x-card class="mt-8 border-cyan-400/30 bg-cyan-400/10">
            <h2 class="text-2xl font-semibold">Suggested Garcia Systems support</h2>
            <p class="mt-3 text-slate-200">{{ $assessment->service_cta ?: $result['service_cta'] }}</p>
            <a class="mt-5 inline-block text-cyan-300" href="{{ route('contact') }}">Contact Garcia Systems →</a>
        </x-card>
    </section>
</x-layouts.app>
