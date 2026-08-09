@props(['title', 'description' => null, 'href' => null, 'linkText' => null])

<x-card {{ $attributes->merge(['class' => 'h-full']) }}>
    <h3 class="text-xl font-semibold text-slate-50">{{ $title }}</h3>
    @if($description)
        <p class="mt-3 text-slate-300">{{ $description }}</p>
    @endif
    {{ $slot }}
    @if($href && $linkText)
        <a class="gs-link gs-focus mt-5 inline-flex rounded-sm" href="{{ $href }}">{{ $linkText }} <span aria-hidden="true">→</span></a>
    @endif
</x-card>
