@props(['number', 'title', 'description'])

<div {{ $attributes->merge(['class' => 'relative rounded-2xl border border-white/10 bg-slate-900/80 p-5 shadow-sm']) }}>
    <div class="flex items-center gap-3">
        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-cyan-300 text-sm font-bold text-slate-950">{{ $number }}</span>
        <h3 class="text-lg font-semibold">{{ $title }}</h3>
    </div>
    <p class="mt-4 text-sm leading-6 text-slate-300">{{ $description }}</p>
</div>
