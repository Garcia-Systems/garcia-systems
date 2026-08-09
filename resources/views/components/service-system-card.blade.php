@props(['title', 'description', 'href', 'index' => 0])

@php
    $motifs = [
        ['Signal', 'Scope', 'Roadmap'],
        ['Need', 'System', 'Data'],
        ['Work', 'Rule', 'Flow'],
        ['Business', 'Bridge', 'Delivery'],
        ['Context', 'Readiness', 'Pilot'],
        ['Plan', 'Launch', 'Learn'],
    ];
    $labels = $motifs[$index % count($motifs)];
@endphp

<article class="gs-service-system-card gs-focus" data-reveal tabindex="0">
    <div class="gs-service-diagram gs-service-diagram-{{ ($index % 3) + 1 }}" aria-label="{{ implode(' to ', $labels) }} system path">
        <svg viewBox="0 0 360 84" preserveAspectRatio="none" aria-hidden="true" focusable="false">
            <path d="M36 42 C88 42 87 20 142 20 S218 64 270 42 L328 42" />
            <path class="gs-service-path-active" d="M36 42 C88 42 87 20 142 20 S218 64 270 42 L328 42" />
        </svg>
        @foreach($labels as $label)
            <span class="gs-service-node"><i aria-hidden="true"></i>{{ $label }}</span>
        @endforeach
    </div>
    <div class="p-6 pt-5">
        <p class="gs-eyebrow">Capability {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</p>
        <h3 class="mt-3 text-xl font-semibold text-slate-50">{{ $title }}</h3>
        <p class="mt-3 leading-7 text-slate-300">{{ $description }}</p>
        <a class="gs-link gs-focus mt-6 inline-block rounded-sm" href="{{ $href }}">View service <span aria-hidden="true">→</span></a>
    </div>
</article>
