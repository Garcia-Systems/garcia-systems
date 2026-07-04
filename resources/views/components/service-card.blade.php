@props(['service'])

<x-card class="p-0 overflow-hidden">
    <div class="grid gap-0 lg:grid-cols-[1.05fr_.95fr]">
        <div class="p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-cyan-300">{{ $service['label'] }}</p>
            <h2 class="mt-3 text-2xl font-bold">{{ $service['title'] }}</h2>
            <div class="mt-5 space-y-5 text-slate-300">
                <div><h3 class="font-semibold text-slate-100">Overview</h3><p class="mt-1">{{ $service['overview'] }}</p></div>
                <div><h3 class="font-semibold text-slate-100">Business problems addressed</h3><p class="mt-1">{{ $service['problems'] }}</p></div>
                <div><h3 class="font-semibold text-slate-100">Expected outcomes</h3><p class="mt-1">{{ $service['outcomes'] }}</p></div>
                <div><h3 class="font-semibold text-slate-100">Ideal clients</h3><p class="mt-1">{{ $service['clients'] }}</p></div>
            </div>
            <a class="mt-7 inline-flex rounded-full border border-cyan-300/40 px-5 py-2 font-semibold text-cyan-300" href="{{ route('contact') }}">{{ $service['cta'] }}</a>
        </div>
        <div class="border-t border-white/10 bg-slate-950/60 p-8 lg:border-l lg:border-t-0">
            <h3 class="font-semibold text-slate-100">Deliverables</h3>
            <ul class="mt-4 grid gap-2 text-sm text-slate-300 sm:grid-cols-2 lg:grid-cols-1">
                @foreach($service['deliverables'] as $deliverable)
                    <li class="rounded-xl bg-white/5 px-3 py-2">{{ $deliverable }}</li>
                @endforeach
            </ul>
            <h3 class="mt-7 font-semibold text-slate-100">Example engagements</h3>
            <ul class="mt-4 space-y-3 text-sm text-slate-300">
                @foreach($service['examples'] as $example)
                    <li class="border-l border-cyan-300/40 pl-3">{{ $example }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</x-card>
