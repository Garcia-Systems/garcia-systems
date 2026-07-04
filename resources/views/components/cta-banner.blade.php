@props(['title', 'description', 'href', 'linkText'])

<section {{ $attributes->merge(['class' => 'mx-auto max-w-6xl px-6 py-16']) }}>
    <div class="overflow-hidden rounded-3xl border border-cyan-300/30 bg-gradient-to-br from-cyan-300/15 via-slate-900 to-slate-950 p-8 md:p-10">
        <h2 class="max-w-4xl text-3xl font-bold tracking-tight">{{ $title }}</h2>
        <p class="mt-4 max-w-3xl text-slate-300">{{ $description }}</p>
        <a class="mt-7 inline-flex rounded-full bg-cyan-400 px-6 py-3 font-semibold text-slate-950" href="{{ $href }}">{{ $linkText }}</a>
    </div>
</section>
