@props(['dimensions'])

<div class="gs-readiness" data-ai-readiness>
    <div class="gs-readiness-copy">
        <p class="gs-eyebrow">Technical assessment</p>
        <h2 class="mt-3 text-3xl font-bold tracking-tight md:text-5xl">Should AI be used here?</h2>
        <p class="mt-4 max-w-2xl text-lg text-slate-300">Readiness is an operating condition, not an enthusiasm score. The assessment tests the foundation for a focused, measurable AI or automation pilot.</p>
        <div class="gs-readiness-sequence" aria-label="Assessment to execution sequence">
            @foreach(['Assess', 'Model', 'Build', 'Measure'] as $step)<span>{{ $step }}</span>@endforeach
        </div>
        <x-glow-button class="mt-7" :href="route('assessment')">Take the AI Readiness Assessment</x-glow-button>
    </div>
    <div class="gs-readiness-instrument" aria-labelledby="readiness-instrument-title">
        <div class="gs-readiness-head">
            <div><p class="gs-eyebrow">AI readiness</p><h3 id="readiness-instrument-title">Assessment preview</h3></div>
            <span class="gs-readiness-state">Awaiting inputs</span>
        </div>
        <div class="gs-readiness-body">
            <div class="gs-readiness-gauge" role="img" aria-label="Four assessment dimensions. Complete the assessment to calculate your score and readiness tier.">
                <svg viewBox="0 0 180 180" aria-hidden="true"><circle class="gs-gauge-track" cx="90" cy="90" r="68"/><circle class="gs-gauge-value" cx="90" cy="90" r="68" pathLength="100"/></svg>
                <div><strong>{{ $dimensions->count() }}</strong><span>evaluation<br>dimensions</span></div>
            </div>
            <p class="gs-readiness-score-text"><strong>No score assigned</strong><span>Your answers calculate an overall score, category breakdown, and readiness tier.</span></p>
        </div>
        <ul class="gs-readiness-dimensions" aria-label="Readiness dimensions">
            @forelse($dimensions as $index => $dimension)
                <li style="--dimension-index: {{ $index }}">
                    <button type="button" aria-expanded="false">
                        <span><b>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</b>{{ $dimension['label'] }}</span><i aria-hidden="true"><em></em></i>
                    </button>
                    <p>{{ $dimension['question'] }} @if(filled($dimension['help'] ?? null)) {{ $dimension['help'] }} @endif</p>
                </li>
            @empty
                <li class="gs-readiness-empty">Assessment dimensions are being reviewed. The full assessment will return when they are available.</li>
            @endforelse
        </ul>
    </div>
</div>
