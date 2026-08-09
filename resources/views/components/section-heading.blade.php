@props(['eyebrow' => null, 'title', 'description' => null, 'align' => 'left'])

<div {{ $attributes->merge(['class' => $align === 'center' ? 'mx-auto max-w-3xl text-center' : 'max-w-3xl']) }}>
    @if($eyebrow)
        <p class="gs-eyebrow">{{ $eyebrow }}</p>
    @endif
    <h2 class="mt-3 text-3xl font-bold tracking-[-0.025em] text-slate-50 md:text-5xl">{{ $title }}</h2>
    @if($description)
        <p class="mt-4 text-lg text-slate-300">{{ $description }}</p>
    @endif
</div>
