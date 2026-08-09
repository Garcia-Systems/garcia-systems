@props(['laboratory'])

<article class="gs-panel flex h-full flex-col p-6">
    <div class="flex items-center justify-between gap-3">
        <p class="gs-eyebrow">{{ $laboratory['domain'] }}</p>
        <span class="text-xs text-slate-500">{{ $laboratory['status'] }}</span>
    </div>
    <h3 class="mt-4 text-2xl font-semibold text-white">{{ $laboratory['title'] }}</h3>
    <p class="mt-3 leading-7 text-slate-300">{{ $laboratory['description'] }}</p>
    <ol class="mt-6 flex flex-wrap items-center gap-2" aria-label="{{ $laboratory['title'] }} architecture">
        @foreach($laboratory['architecture'] as $node)
            <li class="flex items-center gap-2 text-xs text-slate-300">
                @if(! $loop->first)<span class="text-cyan-400" aria-hidden="true">→</span>@endif
                <span class="rounded-md border border-cyan-200/15 bg-cyan-300/5 px-2.5 py-2">{{ $node }}</span>
            </li>
        @endforeach
    </ol>
    <ul class="mt-5 flex flex-wrap gap-2" aria-label="Technologies and concepts">
        @foreach(array_slice(array_merge($laboratory['technologies'], $laboratory['key_concepts']), 0, 6) as $tag)
            <li class="rounded-full border border-white/10 px-3 py-1 text-xs text-slate-400">{{ $tag }}</li>
        @endforeach
    </ul>
    <a class="gs-link gs-focus mt-auto inline-block pt-6" href="{{ $laboratory['repository_url'] }}" target="_blank" rel="noopener noreferrer">Explore on GitHub <span aria-hidden="true">↗</span></a>
</article>
