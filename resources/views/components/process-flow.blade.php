@props(['steps'])

<div class="gs-process-flow mt-10" data-process-flow style="--process-progress: 1">
    <div class="gs-process-track" aria-hidden="true"><span></span></div>
    <div class="gs-process-steps">
        @foreach($steps as $index => [$step, $description])
            <x-process-step :number="$index + 1" :title="$step" :description="$description" style="--step-index: {{ $index }}" />
        @endforeach
    </div>
    <div class="gs-process-complete" aria-label="Process completion produces measurable impact">
        <span aria-hidden="true">✓</span> Measurable impact
    </div>
</div>
