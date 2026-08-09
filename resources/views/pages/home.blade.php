<x-layouts.app title="Garcia Systems" page-description="Business-first systems consulting for products, systems, automation, and AI-ready workflows that teams can adopt and measure.">
    @php
        $process = [
            ['Discover','Frame the business problem, stakeholders, and workflow context.'],
            ['Analyze','Find the operating friction, data constraints, and value drivers.'],
            ['Design','Shape practical systems, product paths, or automation options.'],
            ['Execute','Move from decision to roadmap, launch, measurement, and iteration.'],
        ];
        $services = [
            ['Product Discovery','Turn unclear product ideas into requirements, MVP definitions, and roadmap decisions.'],
            ['Solutions Engineering','Design internal tools, integrations, dashboards, and technical recommendations.'],
            ['Workflow Modernization','Redesign repeatable work so systems and automation have a stable foundation.'],
            ['Technical Liaison Services','Translate between business stakeholders, vendors, software teams, and leadership.'],
            ['AI Opportunity Assessment','Prioritize practical AI and automation pilots by value, readiness, and risk.'],
            ['Product Execution Support','Create execution rhythm, backlog clarity, launch plans, and iteration loops.'],
        ];
    @endphp

    <section class="gs-shell gs-section relative pt-16 md:pt-24">
        <div class="grid items-center gap-12 lg:grid-cols-[1.05fr_.95fr] lg:gap-16">
            <div>
                <p class="gs-eyebrow">Business-first systems consulting</p>
                <h1 aria-label="Turning Business Problems Into Products, Systems, and Intelligent Workflows" class="mt-5 text-4xl font-bold leading-[1.04] tracking-[-0.045em] text-slate-50 sm:text-5xl md:text-6xl xl:text-7xl">Turning Business Problems Into <span class="gs-gradient-text">Products, Systems, and Intelligent Workflows</span></h1>
                <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-300">Garcia Systems helps teams understand operational friction, prioritize technology opportunities, and ship focused improvements that are practical enough to adopt and specific enough to measure.</p>
                <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <x-glow-button :href="route('contact')">Start a conversation <span aria-hidden="true">→</span></x-glow-button>
                    <x-glow-button :href="route('services')" variant="secondary">Explore services</x-glow-button>
                </div>
                <ul class="mt-9 flex flex-wrap gap-x-7 gap-y-3 text-sm text-slate-400" aria-label="Consulting principles">
                    @foreach(['Business-first thinking', 'Systems thinking', 'Measurable impact'] as $principle)
                        <li class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-cyan-300 shadow-[0_0_10px_rgb(103_232_249)]" aria-hidden="true"></span>{{ $principle }}</li>
                    @endforeach
                </ul>
            </div>
            <div data-hero-visualization><x-system-visual /></div>
        </div>
        <div class="mt-16 grid gap-4 md:grid-cols-3" data-reveal>
            <x-feature-card title="Business clarity before tools" description="Start with the problem, operating model, stakeholders, and outcome before choosing a technical path." />
            <x-feature-card title="Measured modernization" description="Improve workflows in phases so teams can validate assumptions and avoid unnecessary complexity." />
            <x-feature-card title="Practical AI readiness" description="Identify where AI can support real work after workflow quality, data dependencies, and risk are understood." />
        </div>
    </section>

    <x-friction-system-section />

    <section class="gs-shell gs-section">
        <x-section-heading eyebrow="How I work" title="How Garcia Systems Works" description="A practical consulting rhythm for moving from ambiguous friction to decisions, plans, and execution." />
        <x-process-flow :steps="$process" />
    </section>

    <section class="gs-shell gs-section">
        <x-section-heading eyebrow="Services summary" title="Focused consulting paths for product, workflow, AI, and execution work." description="Use Garcia Systems when the business problem is real, the path is unclear, and the team needs practical analysis and delivery structure." />
        <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($services as $index => [$title, $description])
                <x-service-system-card :title="$title" :description="$description" :href="route('services')" :index="$index" />
            @endforeach
        </div>
    </section>

    <section class="gs-shell gs-section">
        <div class="gs-panel overflow-hidden p-7 md:p-10">
            <div class="absolute inset-0 opacity-30 [background-image:radial-gradient(circle_at_70%_45%,rgb(34_211_238/.16),transparent_30%)]" aria-hidden="true"></div>
            <div class="relative">
                <div class="max-w-3xl"><x-section-heading eyebrow="Opportunity Atlas" title="Trace the work before choosing the technology." description="Start with what work is happening. Follow the workflow to observable friction, then shape a practical solution pattern around the real operating context." /></div>
                <div class="mt-10"><x-opportunity-atlas-map :chains="$atlasChains" /></div>
                <a class="gs-link gs-focus mt-8 inline-block rounded-sm" href="{{ route('atlas') }}">Explore the Opportunity Atlas <span aria-hidden="true">→</span></a>
            </div>
        </div>
    </section>

    <section class="gs-shell gs-section">
        <div class="gs-panel grid gap-10 overflow-hidden border-cyan-300/30 p-8 md:grid-cols-[1fr_auto] md:items-center md:p-12">
            <div><p class="gs-eyebrow">Technical assessment</p><h2 class="mt-3 text-3xl font-bold tracking-tight md:text-5xl">Explore AI Readiness</h2><p class="mt-4 max-w-3xl text-lg text-slate-300">Use the assessment to evaluate workflow clarity, data quality, ownership, and risk before selecting an AI or automation pilot.</p><x-glow-button class="mt-7" :href="route('assessment')">Take the assessment</x-glow-button></div>
            <div class="relative mx-auto flex h-48 w-48 items-center justify-center rounded-full bg-[conic-gradient(from_220deg,#5ee7f7_0_62%,rgb(94_231_247/.08)_62%)] p-2 shadow-[0_0_55px_rgb(34_211_238/.12)]" role="img" aria-label="Readiness assessment gauge illustration"><div class="flex h-full w-full flex-col items-center justify-center rounded-full bg-[#06101e]"><span class="text-4xl font-bold text-cyan-200">4</span><span class="mt-1 text-xs uppercase tracking-[.2em] text-slate-400">dimensions</span></div></div>
        </div>
    </section>

    @foreach([['Latest Thinking','Featured articles','Practical writing on automation, workflow clarity, product decisions, and AI readiness.','articles'], ['Featured videos','Short explainers for operational and automation decisions.',null,'videos']] as [$eyebrow,$heading,$description,$type])
        <section class="gs-shell py-14 md:py-20">
            <x-section-heading :eyebrow="$eyebrow" :title="$heading" :description="$description" />
            <div class="mt-8 grid gap-5 md:grid-cols-3">
                @if($type === 'articles')
                    @forelse($articles as $article)<x-feature-card :title="$article->title" :description="$article->excerpt" :href="route('articles.show',$article)" linkText="Read" />@empty<x-card><h3 class="text-xl font-semibold">Articles are coming soon.</h3><p class="mt-2 text-slate-300">Check back for practical notes on systems, workflows, and AI opportunity assessment.</p></x-card>@endforelse
                @else
                    @forelse($videos as $video)<x-card><p class="gs-eyebrow">Video</p><h3 class="mt-3 text-xl font-semibold">{{ $video->title }}</h3><p class="mt-2 text-slate-300">{{ $video->description }}</p><a class="gs-link gs-focus mt-5 inline-block rounded-sm" href="{{ $video->url }}">Watch <span aria-hidden="true">→</span></a></x-card>@empty<x-card><h3 class="text-xl font-semibold">Video library is coming soon.</h3><p class="mt-2 text-slate-300">Expect concise walkthroughs focused on decision-making, workflows, and practical AI use cases.</p></x-card>@endforelse
                @endif
            </div>
        </section>
    @endforeach

    <section class="gs-shell gs-section pb-8">
        <div class="gs-panel overflow-hidden border-cyan-300/30 px-7 py-14 text-center md:px-12 md:py-20"><div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_100%,rgb(34_211_238/.16),transparent_46%)]" aria-hidden="true"></div><div class="relative"><p class="gs-eyebrow">Build the right system</p><h2 class="mx-auto mt-4 max-w-3xl text-3xl font-bold tracking-tight md:text-5xl">Turn operational friction into a measurable execution path.</h2><p class="mx-auto mt-5 max-w-2xl text-slate-300">Start with a focused conversation about the business problem, the people involved, and the outcome that matters.</p><x-glow-button class="mt-8" :href="route('contact')">Start a conversation <span aria-hidden="true">→</span></x-glow-button></div></div>
    </section>
</x-layouts.app>
