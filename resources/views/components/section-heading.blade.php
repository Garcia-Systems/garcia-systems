@props(['eyebrow' => null, 'title', 'description' => null, 'align' => 'left'])

<div {{ $attributes->merge(['class' => $align === 'center' ? 'mx-auto max-w-3xl text-center' : 'max-w-3xl']) }}>
    @if($eyebrow)
        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-300">{{ $eyebrow }}</p>
    @endif
    <h2 class="mt-3 text-3xl font-bold tracking-tight md:text-4xl">{{ $title }}</h2>
    @if($description)
        <p class="mt-4 text-lg text-slate-300">{{ $description }}</p>
    @endif
</div>
