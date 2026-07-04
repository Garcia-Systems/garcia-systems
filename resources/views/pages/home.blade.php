<x-layouts.app title="Garcia Systems">
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

    <section class="relative mx-auto max-w-6xl px-6 py-20 md:py-24">
        <div class="max-w-5xl">
            <p class="font-semibold text-cyan-300">Business-first systems consulting</p>
            <h1 class="mt-4 text-5xl font-bold tracking-tight md:text-7xl">Turning Business Problems Into Products, Systems, and Intelligent Workflows</h1>
            <p class="mt-6 max-w-3xl text-lg leading-8 text-slate-300">Garcia Systems helps teams understand operational friction, prioritize technology opportunities, and ship focused improvements that are practical enough to adopt and specific enough to measure.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a class="rounded-full bg-cyan-400 px-6 py-3 font-semibold text-slate-950" href="{{ route('contact') }}">Start a conversation</a>
                <a class="rounded-full border border-white/15 px-6 py-3 font-semibold text-slate-100" href="{{ route('services') }}">Explore services</a>
            </div>
        </div>
        <div class="mt-10 grid gap-4 md:grid-cols-3">
            <x-feature-card title="Business clarity before tools" description="Start with the problem, operating model, stakeholders, and outcome before choosing a technical path." />
            <x-feature-card title="Measured modernization" description="Improve workflows in phases so teams can validate assumptions and avoid unnecessary complexity." />
            <x-feature-card title="Practical AI readiness" description="Identify where AI can support real work after workflow quality, data dependencies, and risk are understood." />
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-12">
        <x-section-heading eyebrow="How I work" title="How Garcia Systems Works" description="A practical consulting rhythm for moving from ambiguous friction to decisions, plans, and execution." />
        <div class="mt-8 grid gap-4 md:grid-cols-4">
            @foreach($process as $index => [$step, $description])
                <x-process-step :number="$index + 1" :title="$step" :description="$description" />
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-12">
        <x-section-heading eyebrow="Services summary" title="Focused consulting paths for product, workflow, AI, and execution work." description="Use Garcia Systems when the business problem is real, the path is unclear, and the team needs practical analysis and delivery structure." />
        <div class="mt-8 grid gap-5 md:grid-cols-3">
            @foreach($services as [$title, $description])
                <x-feature-card :title="$title" :description="$description" :href="route('services')" linkText="View service" />
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-12">
        <div class="grid gap-8 lg:grid-cols-[.9fr_1.1fr] lg:items-start">
            <x-section-heading eyebrow="Opportunity Atlas" title="Preview common friction points before choosing a solution." description="The Opportunity Atlas connects industries, workflows, friction points, and solution patterns so technology decisions can start from observable business problems." />
            <div class="grid gap-5">
                @forelse($frictions as $f)
                    <x-card>
                        <p class="text-sm font-semibold text-cyan-300">{{ $f->workflow?->industry?->name }} {{ $f->workflow ? '• '.$f->workflow->name : '' }}</p>
                        <h3 class="mt-2 text-xl font-semibold">{{ $f->name }}</h3>
                        <p class="mt-2 text-slate-300">{{ $f->description }}</p>
                    </x-card>
                @empty
                    <x-card><h3 class="text-xl font-semibold">Workflow friction preview</h3><p class="mt-2 text-slate-300">Browse the atlas to connect business friction with practical solution patterns.</p></x-card>
                @endforelse
                <a class="font-semibold text-cyan-300" href="{{ route('atlas') }}">Explore the Opportunity Atlas →</a>
            </div>
        </div>
    </section>

    <x-cta-banner class="py-12" title="Explore AI Readiness" description="Use the assessment to evaluate workflow clarity, data quality, ownership, and risk before selecting an AI or automation pilot." :href="route('assessment')" linkText="Take the assessment" />

    <section class="mx-auto max-w-6xl px-6 py-12">
        <x-section-heading eyebrow="Latest Thinking" title="Featured articles" description="Practical writing on automation, workflow clarity, product decisions, and AI readiness." />
        <div class="mt-8 grid gap-5 md:grid-cols-3">
            @forelse($articles as $article)
                <x-feature-card :title="$article->title" :description="$article->excerpt" :href="route('articles.show',$article)" linkText="Read" />
            @empty
                <x-card><h3 class="text-xl font-semibold">Articles are coming soon.</h3><p class="mt-2 text-slate-300">Check back for practical notes on systems, workflows, and AI opportunity assessment.</p></x-card>
            @endforelse
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-12">
        <x-section-heading eyebrow="Featured videos" title="Short explainers for operational and automation decisions." />
        <div class="mt-8 grid gap-5 md:grid-cols-3">
            @forelse($videos as $video)
                <x-card>
                    <h3 class="text-xl font-semibold">{{ $video->title }}</h3>
                    <p class="mt-2 text-slate-300">{{ $video->description }}</p>
                    <a class="mt-4 inline-block font-semibold text-cyan-300" href="{{ $video->url }}">Watch →</a>
                </x-card>
            @empty
                <x-card><h3 class="text-xl font-semibold">Video library is coming soon.</h3><p class="mt-2 text-slate-300">Expect concise walkthroughs focused on decision-making, workflows, and practical AI use cases.</p></x-card>
            @endforelse
        </div>
    </section>
</x-layouts.app>
