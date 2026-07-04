@props(['title', 'description' => null, 'href' => null, 'linkText' => null])

<x-card {{ $attributes->merge(['class' => 'h-full transition hover:border-cyan-300/40 hover:bg-white/10']) }}>
    <h3 class="text-xl font-semibold text-slate-50">{{ $title }}</h3>
    @if($description)
        <p class="mt-3 text-slate-300">{{ $description }}</p>
    @endif
    {{ $slot }}
    @if($href && $linkText)
        <a class="mt-5 inline-flex font-semibold text-cyan-300" href="{{ $href }}">{{ $linkText }} →</a>
    @endif
</x-card>
