@props(['number', 'title', 'description'])

<div {{ $attributes->merge(['class' => 'gs-panel gs-card relative p-5']) }} data-reveal>
    <div class="flex items-center gap-3">
        <span class="flex h-10 w-10 items-center justify-center rounded-full border border-cyan-200/50 bg-cyan-300/10 text-sm font-bold text-cyan-200 shadow-[0_0_24px_rgb(34_211_238/.12)]">0{{ $number }}</span>
        <h3 class="text-lg font-semibold">{{ $title }}</h3>
    </div>
    <p class="mt-4 text-sm leading-6 text-slate-300">{{ $description }}</p>
</div>
