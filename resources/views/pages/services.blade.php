<x-layouts.app title="Services">
    @php
        $services = [
            [
                'title' => 'Product Discovery',
                'what' => 'A focused discovery engagement that turns a fuzzy operational need into a clear product direction, prioritized requirements, and an implementation path.',
                'problem' => 'Teams know something is slowing them down, but they do not yet have a shared definition of the user, workflow, constraints, or business case.',
                'deliverables' => ['Stakeholder and workflow interviews', 'Problem framing and opportunity brief', 'Prioritized feature map', 'Pilot scope and decision criteria'],
                'helps' => 'Founders, operators, department leads, and product owners who need clarity before investing in a build.',
            ],
            [
                'title' => 'Solutions Engineering',
                'what' => 'Practical technical design and implementation support for internal tools, integrations, dashboards, and lightweight applications.',
                'problem' => 'Important work is trapped between spreadsheets, disconnected software, and manual handoffs that make growth harder to manage.',
                'deliverables' => ['Current-state systems map', 'Solution architecture and build plan', 'Prototype or MVP implementation support', 'Documentation for handoff and iteration'],
                'helps' => 'Growing teams that need useful systems without turning every improvement into a large software project.',
            ],
            [
                'title' => 'Workflow Modernization',
                'what' => 'An operations-first service for redesigning recurring workflows so information moves cleanly, accountability is visible, and automation has a stable foundation.',
                'problem' => 'People are spending too much time chasing status, re-entering information, reconciling mistakes, or compensating for unclear process ownership.',
                'deliverables' => ['Workflow audit', 'Bottleneck and risk analysis', 'Future-state process design', 'Automation and tooling recommendations'],
                'helps' => 'Operations, service, finance, customer success, and back-office teams with repeatable work that has outgrown informal processes.',
            ],
            [
                'title' => 'Technical Liaison Services',
                'what' => 'A translation layer between business stakeholders, vendors, software teams, and leadership so technical work stays aligned with operational goals.',
                'problem' => 'Business teams and technical partners are using different language, creating misaligned expectations, slow decisions, and avoidable rework.',
                'deliverables' => ['Requirements translation', 'Vendor and implementation partner coordination', 'Decision memos for leadership', 'Acceptance criteria and launch readiness support'],
                'helps' => 'Organizations that need someone to connect business intent with technical execution without adding another full-time role.',
            ],
            [
                'title' => 'AI Opportunity Assessment',
                'what' => 'A grounded review of where AI, automation, or decision support could improve measurable work without forcing AI into places it does not belong.',
                'problem' => 'Leaders are under pressure to use AI but need to separate practical opportunities from risky, vague, or low-value experiments.',
                'deliverables' => ['AI readiness review', 'Opportunity shortlist', 'Risk and data dependency notes', 'Recommended pilot sequence'],
                'helps' => 'Executives, operators, and innovation leads who want a pragmatic AI roadmap connected to real workflows.',
            ],
            [
                'title' => 'Product Execution Support',
                'what' => 'Hands-on support for moving a defined product, tool, or automation initiative from plan to shipped improvement.',
                'problem' => 'Teams have agreed on the need but lack the product management capacity, implementation rhythm, or cross-functional follow-through to get it live.',
                'deliverables' => ['Execution roadmap', 'Sprint and milestone planning', 'Backlog shaping and acceptance criteria', 'Launch, feedback, and iteration support'],
                'helps' => 'Teams with a validated direction that need disciplined execution support to turn decisions into outcomes.',
            ],
        ];
    @endphp

    <section class="mx-auto max-w-6xl px-6 py-16">
        <p class="text-cyan-300 font-semibold">Services</p>
        <h1 class="mt-4 max-w-4xl text-4xl font-bold tracking-tight md:text-5xl">Consulting services for practical systems, workflows, and product execution.</h1>
        <p class="mt-6 max-w-3xl text-lg text-slate-300">Garcia Systems helps teams clarify business problems, modernize operations, and ship focused technical improvements without hype, unnecessary complexity, or premature platform bets.</p>
        <a class="mt-8 inline-block rounded-full bg-cyan-400 px-6 py-3 font-semibold text-slate-950" href="{{ route('contact') }}">Talk about your service need</a>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-8">
        <div class="grid gap-6">
            @foreach($services as $service)
                <x-card class="p-8">
                    <div class="grid gap-8 lg:grid-cols-[1.1fr_.9fr]">
                        <div>
                            <h2 class="text-2xl font-bold">{{ $service['title'] }}</h2>
                            <div class="mt-5 space-y-4 text-slate-300">
                                <p><span class="font-semibold text-slate-100">What it is:</span> {{ $service['what'] }}</p>
                                <p><span class="font-semibold text-slate-100">Business problem it solves:</span> {{ $service['problem'] }}</p>
                                <p><span class="font-semibold text-slate-100">Who it helps:</span> {{ $service['helps'] }}</p>
                            </div>
                            <a class="mt-6 inline-block text-cyan-300" href="{{ route('contact') }}">Discuss {{ $service['title'] }} →</a>
                        </div>
                        <div class="rounded-xl bg-slate-950/60 p-5">
                            <h3 class="font-semibold text-slate-100">Typical deliverables</h3>
                            <ul class="mt-4 space-y-2 text-slate-300">
                                @foreach($service['deliverables'] as $deliverable)
                                    <li>• {{ $deliverable }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-16">
        <h2 class="text-3xl font-bold">How Garcia Systems Works</h2>
        <div class="mt-6 grid gap-5 md:grid-cols-4">
            @foreach([
                ['Diagnose', 'Map the workflow, stakeholders, systems, risks, and business outcome that matter.'],
                ['Prioritize', 'Separate urgent symptoms from high-leverage opportunities worth solving first.'],
                ['Design', 'Shape a practical solution path, success criteria, and implementation plan.'],
                ['Execute', 'Support the build, vendor coordination, launch, and iteration needed to make change stick.'],
            ] as [$step, $description])
                <x-card>
                    <h3 class="text-xl font-semibold">{{ $step }}</h3>
                    <p class="mt-3 text-slate-300">{{ $description }}</p>
                </x-card>
            @endforeach
        </div>
    </section>

    <section class="mx-auto grid max-w-6xl gap-5 px-6 py-8 md:grid-cols-2">
        <x-card class="border-cyan-300/30">
            <h2 class="text-2xl font-bold">Best fit</h2>
            <ul class="mt-4 space-y-3 text-slate-300">
                <li>• You have a real operational bottleneck, growth constraint, or product execution gap.</li>
                <li>• You want practical recommendations tied to measurable business outcomes.</li>
                <li>• You can involve the people who understand the workflow and own the result.</li>
            </ul>
        </x-card>
        <x-card>
            <h2 class="text-2xl font-bold">Not best fit</h2>
            <ul class="mt-4 space-y-3 text-slate-300">
                <li>• You want AI or automation added before clarifying the underlying workflow.</li>
                <li>• You need a large agency build team or a fully outsourced software department.</li>
                <li>• You are not ready to make decisions, share context, or support implementation.</li>
            </ul>
        </x-card>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-16">
        <div class="rounded-3xl border border-cyan-300/30 bg-cyan-300/10 p-8 md:p-10">
            <h2 class="text-3xl font-bold">Have a workflow, product, or AI question that needs a practical path forward?</h2>
            <p class="mt-4 max-w-3xl text-slate-300">Start with a conversation about the business problem, the people affected, and what a useful first phase should produce.</p>
            <a class="mt-6 inline-block rounded-full bg-cyan-400 px-6 py-3 font-semibold text-slate-950" href="{{ route('contact') }}">Contact Garcia Systems</a>
        </div>
    </section>
</x-layouts.app>
