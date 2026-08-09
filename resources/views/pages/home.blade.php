<x-layouts.app title="Garcia Systems" page-description="Garcia Systems makes complex information systems understandable, executable, and improvable through systems engineering, deterministic simulation, software integration, and workflow modernization.">
    @php
        $process = [
            ['Understand','Map the problem, actors, information, workflow, boundaries, constraints, and friction.'],
            ['Model','Make behavior and assumptions explicit through diagrams, scenarios, and executable models.'],
            ['Improve','Apply the evidence through software, integrations, workflow modernization, automation, and technical design.'],
            ['Verify','Test claims with observable behavior, controlled scenarios, and measurable outcomes.'],
        ];
        $services = [
            ['Systems Analysis & Discovery','Understand workflows, actors, data, constraints, and technical boundaries before selecting a solution.'],
            ['Software & Integration Engineering','Build applications, APIs, adapters, workflows, and integrations around real operational systems.'],
            ['Executable Modeling & Simulation','Turn complicated systems and policies into deterministic models teams can inspect, run, test, and change.'],
            ['Workflow Automation & AI Enablement','Use automation or AI where workflow quality, data, ownership, risk, and measurable value support it.'],
            ['Technical Translation & Solutions Engineering','Connect business needs, architecture, vendors, leadership, and implementation teams.'],
        ];
    @endphp

    <section class="gs-shell gs-section relative pt-16 md:pt-24">
        <div class="grid items-center gap-12 lg:grid-cols-[1.05fr_.95fr] lg:gap-16">
            <div>
                <p class="gs-eyebrow">Systems • Software • Executable models</p>
                <h1 class="mt-5 text-4xl font-bold leading-[1.04] tracking-[-0.045em] text-slate-50 sm:text-5xl md:text-6xl xl:text-7xl">Make Complex Systems <span class="gs-gradient-text">Understandable, Executable, and Improvable.</span></h1>
                <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-300">Garcia Systems studies how information, people, software, and workflows interact—then turns that understanding into inspectable models, practical systems, integrations, and evidence-led improvements.</p>
                <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <x-glow-button :href="route('laboratories')">Explore laboratories <span aria-hidden="true">→</span></x-glow-button>
                    <x-glow-button :href="route('services')" variant="secondary">Explore services</x-glow-button>
                </div>
                <ul class="mt-9 flex flex-wrap gap-x-7 gap-y-3 text-sm text-slate-400" aria-label="Systems principles">
                    @foreach(['Make assumptions explicit', 'Prefer reproducible evidence', 'Understand before changing'] as $principle)<li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-cyan-300" aria-hidden="true"></span>{{ $principle }}</li>@endforeach
                </ul>
            </div>
            <div data-hero-visualization><x-system-visual /></div>
        </div>
    </section>

    <x-friction-system-section />

    <section class="gs-shell gs-section">
        <x-section-heading eyebrow="The Garcia Systems model" title="Understand. Model. Improve. Verify." description="A systems discipline that connects analysis and learning to practical engineering—and checks every important claim against observable evidence." />
        <x-process-flow :steps="$process" />
    </section>

    <section class="gs-shell gs-section">
        <x-section-heading eyebrow="Practical capabilities" title="Services grounded in system understanding." description="Commercial work applies the same inspectable, evidence-led approach demonstrated in the laboratories." />
        <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($services as $index => [$title, $description])<x-service-system-card :title="$title" :description="$description" :href="route('services')" :index="$index" />@endforeach
        </div>
    </section>

    <section class="gs-shell gs-section">
        <x-section-heading eyebrow="Executable laboratories" title="Executable systems, not static case studies." description="Deterministic laboratories turn complex information-system concepts into runnable, inspectable models. Read the reasoning, change an assumption, and verify what happens." />
        <div class="mt-10 grid gap-6 lg:grid-cols-2">
            @foreach($laboratories as $laboratory)<x-laboratory-card :laboratory="$laboratory" />@endforeach
        </div>
        <a class="gs-link gs-focus mt-8 inline-block" href="{{ route('laboratories') }}">Explore the full laboratory collection <span aria-hidden="true">→</span></a>
    </section>

    <section class="gs-shell gs-section">
        <div class="gs-panel overflow-hidden p-7 md:p-10">
            <div class="relative">
                <div class="max-w-3xl"><x-section-heading eyebrow="Understand • Opportunity Atlas" title="Trace the work before choosing the technology." description="Opportunity Atlas helps teams understand a system by following Industry → Workflow → Friction → Solution Pattern. Laboratories model behavior; services improve the system; tests and evidence verify the result." /></div>
                <div class="mt-10"><x-opportunity-atlas-map :chains="$atlasChains" /></div>
                <a class="gs-link gs-focus mt-8 inline-block" href="{{ route('atlas') }}">Explore the Opportunity Atlas <span aria-hidden="true">→</span></a>
            </div>
        </div>
    </section>

    <section class="gs-shell gs-section">
        <div class="gs-panel grid gap-10 border-cyan-300/30 p-8 md:grid-cols-[1fr_.8fr] md:items-center md:p-12">
            <div><p class="gs-eyebrow">Decision framework • No score assigned</p><h2 class="mt-3 text-3xl font-bold tracking-tight md:text-5xl">Explore AI Readiness</h2><p class="mt-4 max-w-3xl text-lg text-slate-300">Evaluate the conditions around a possible AI-assisted workflow before selecting a tool or pilot.</p><p class="mt-4 text-sm text-slate-400">Complete the assessment to generate your readiness profile.</p><x-glow-button class="mt-7" :href="route('assessment')">Take the AI Readiness Assessment</x-glow-button></div>
            <div aria-label="AI Readiness evaluates four unscored dimensions">
                <p class="text-sm font-semibold text-slate-300">AI Readiness evaluates:</p>
                <ul class="mt-4 space-y-3">@foreach(['Workflow Clarity','Data Quality','Ownership','Risk / Governance'] as $dimension)<li class="flex items-center gap-3 text-sm text-slate-300"><span class="h-2.5 w-2.5 rounded-full border border-cyan-300/50" aria-hidden="true"></span>{{ $dimension }}<span class="ml-auto text-xs text-slate-600">Not evaluated</span></li>@endforeach</ul>
            </div>
        </div>
    </section>

    @if($articles->isNotEmpty() || $videos->isNotEmpty())
        <section class="gs-shell py-14 md:py-20">
            <x-section-heading eyebrow="Thinking" title="Supporting knowledge" description="Articles and videos that explain the reasoning behind systems, software, workflows, and responsible AI adoption." />
            <div class="mt-8 grid gap-5 md:grid-cols-3">
                @foreach($articles as $article)<x-feature-card :title="$article->title" :description="$article->excerpt" :href="route('articles.show',$article)" linkText="Read" />@endforeach
                @foreach($videos as $video)<x-card><p class="gs-eyebrow">Video</p><h3 class="mt-3 text-xl font-semibold">{{ $video->title }}</h3><p class="mt-2 text-slate-300">{{ $video->description }}</p><a class="gs-link mt-5 inline-block" href="{{ $video->url }}">Watch →</a></x-card>@endforeach
            </div>
        </section>
    @endif

    <x-cta-banner title="Understand the system before changing it." description="Start with the workflow, boundaries, assumptions, and evidence—then decide what to model, build, integrate, or improve." :href="route('contact')" linkText="Start a conversation" />
</x-layouts.app>
