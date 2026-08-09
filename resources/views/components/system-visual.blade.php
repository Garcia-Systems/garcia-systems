@php
    $nodes = [
        ['problem', 'Business Problem', 'Unclear process, friction, risk, or opportunity'],
        ['people', 'People', 'Capabilities, roles, and collaboration'],
        ['workflow', 'Workflow', 'Processes and steps that drive work'],
        ['data', 'Data', 'Signals, records, constraints, and evidence'],
        ['system', 'System', 'Integrated solutions that scale'],
        ['automation', 'Automation', 'Repeatable work executed reliably'],
        ['outcome', 'Measurable Outcome', 'Observable results that drive impact'],
    ];
@endphp

<figure
    class="gs-system-hero"
    data-hero-system
    aria-labelledby="hero-system-caption"
>
    <div class="gs-system-fallback" data-hero-system-fallback aria-hidden="true">
        <div class="gs-system-fallback-orbit gs-system-fallback-orbit--outer"></div>
        <div class="gs-system-fallback-orbit gs-system-fallback-orbit--inner"></div>
        <svg class="gs-system-fallback-lines" viewBox="0 0 600 600">
            <defs>
                <linearGradient id="hero-system-line" x1="0" x2="1">
                    <stop stop-color="#5ee7f7" stop-opacity=".2" />
                    <stop offset=".5" stop-color="#5ee7f7" stop-opacity=".8" />
                    <stop offset="1" stop-color="#9a82ff" stop-opacity=".24" />
                </linearGradient>
            </defs>
            <g fill="none" stroke="url(#hero-system-line)" stroke-width="1.25">
                <path d="M300 300 C230 280 180 145 94 126 M300 300 C270 225 282 100 300 66 M300 300 C335 234 438 150 512 142 M300 300 C388 290 500 278 550 300 M300 300 C370 352 450 430 488 498 M300 300 C292 390 345 500 354 548 M300 300 C220 360 130 398 72 438" />
            </g>
        </svg>
        <div class="gs-system-fallback-core"><span>GS</span><small>System core</small></div>
        @foreach ($nodes as [$key, $label])
            <span class="gs-system-fallback-node gs-system-fallback-node--{{ $key }}">{{ $label }}</span>
        @endforeach
    </div>

    <div class="gs-system-canvas" data-hero-system-canvas aria-hidden="true"></div>
    <div class="gs-system-labels" data-hero-system-labels role="list" aria-label="System model concepts">
        @foreach ($nodes as [$key, $label, $detail])
            <span class="gs-system-label gs-system-label--{{ $key }}" data-system-node="{{ $key }}" role="listitem">
                <span class="gs-system-label-dot"></span>
                <span><strong>{{ $label }}</strong><small>{{ $detail }}</small></span>
            </span>
        @endforeach
    </div>

    <figcaption id="hero-system-caption" class="sr-only">
        Garcia Systems brings business problems together with people, workflow, and data, organizes them into practical systems and automation, and produces measurable outcomes.
    </figcaption>
</figure>
