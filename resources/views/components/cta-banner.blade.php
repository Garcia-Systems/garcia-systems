@props(['title', 'description', 'href', 'linkText'])

<section {{ $attributes->merge(['class' => 'mx-auto max-w-6xl px-6 py-16']) }}>
    <div class="gs-panel overflow-hidden border-cyan-300/30 p-8 md:p-10">
        <h2 class="max-w-4xl text-3xl font-bold tracking-tight">{{ $title }}</h2>
        <p class="mt-4 max-w-3xl text-slate-300">{{ $description }}</p>
        <x-glow-button class="mt-7" :href="$href">{{ $linkText }}</x-glow-button>
    </div>
</section>
