<x-layouts.app title="Executable Laboratories" page-description="Explore Garcia Systems executable textbooks, deterministic simulations, and inspectable models for information systems, integration, economics, and solutions engineering.">
    <section class="gs-shell gs-section">
        <p class="gs-eyebrow">Executable textbooks and models</p>
        <h1 class="mt-5 max-w-5xl text-4xl font-bold tracking-tight text-white md:text-6xl">Executable Laboratories</h1>
        <p class="mt-6 max-w-3xl text-xl leading-8 text-slate-300">Read the concept. Run the model. Inspect the behavior. Change the assumptions. Verify the result.</p>
        <p class="mt-5 max-w-3xl leading-7 text-slate-400">These projects combine narrative chapters, executable source, command-line demonstrations, diagrams, automated tests, and controlled scenarios. They make systems ideas available for inspection rather than asking readers to accept static claims.</p>
    </section>

    <section class="gs-shell pb-12">
        <div class="gs-panel grid gap-5 p-7 md:grid-cols-3">
            <div><p class="gs-eyebrow">Learn</p><h2 class="mt-2 text-xl font-semibold">Narrative + source</h2><p class="mt-2 text-sm leading-6 text-slate-400">Explanations stay close to the code and assumptions they describe.</p></div>
            <div><p class="gs-eyebrow">Inspect</p><h2 class="mt-2 text-xl font-semibold">Controlled behavior</h2><p class="mt-2 text-sm leading-6 text-slate-400">Deterministic scenarios expose boundaries, dependencies, and outcomes.</p></div>
            <div><p class="gs-eyebrow">Verify</p><h2 class="mt-2 text-xl font-semibold">Tests as evidence</h2><p class="mt-2 text-sm leading-6 text-slate-400">Repeatable commands and tests let readers challenge a claim themselves.</p></div>
        </div>
        <p class="mt-5 text-sm text-slate-500">These are educational and analytical models. They are not presented as production reference architectures.</p>
    </section>

    <section class="gs-shell gs-section pt-8">
        <x-section-heading eyebrow="Curated collection" title="Systems you can run, inspect, and change." description="Each laboratory models a specific domain and publishes its assumptions, architecture, scenarios, and evidence." />
        <div class="mt-10 grid gap-6 lg:grid-cols-2">
            @foreach($laboratories as $laboratory)<x-laboratory-card :laboratory="$laboratory" />@endforeach
        </div>
    </section>
</x-layouts.app>
