<x-layouts.app title="About Garcia Systems" page-description="Garcia Systems uses systems thinking, executable learning, deterministic models, software engineering, and reproducible evidence to understand and improve information systems.">
    <section class="gs-shell gs-section">
        <p class="gs-eyebrow">About Garcia Systems</p>
        <h1 class="mt-4 max-w-5xl text-4xl font-bold text-white md:text-6xl">Understand the system. Make it executable. Improve it with evidence.</h1>
        <p class="mt-6 max-w-3xl text-lg leading-8 text-slate-300">Garcia Systems studies how people, information, software, vendors, and workflows interact. The work connects systems thinking with software engineering so important behavior can be explained, inspected, tested, and improved.</p>
        <div class="mt-12 grid gap-6 md:grid-cols-2">
            @foreach([
                ['Systems before solutions','Map actors, boundaries, information, dependencies, constraints, and friction before choosing technology.'],
                ['Executable learning','Turn important concepts into deterministic models, scenarios, source code, and tests—not diagrams or claims alone.'],
                ['Engineering and integration','Apply the model through focused software, interfaces, adapters, workflow modernization, and responsible automation.'],
                ['Translation and verification','Give business and technical participants shared language, explicit assumptions, observable behavior, and reproducible evidence.'],
            ] as [$title,$copy])<x-card><h2 class="text-xl font-semibold text-white">{{ $title }}</h2><p class="mt-3 leading-7 text-slate-300">{{ $copy }}</p></x-card>@endforeach
        </div>
    </section>
    <section class="gs-shell pb-16"><div class="gs-panel p-8"><p class="gs-eyebrow">Working philosophy</p><p class="mt-4 max-w-4xl text-2xl font-semibold leading-9 text-white">Make assumptions explicit. Expose boundaries and dependencies. Prefer deterministic examples. Test claims. Improve based on what the system shows.</p><div class="mt-7 flex gap-3"><x-glow-button :href="route('laboratories')">See the laboratories</x-glow-button><x-glow-button :href="route('services')" variant="secondary">Explore services</x-glow-button></div></div></section>
</x-layouts.app>
