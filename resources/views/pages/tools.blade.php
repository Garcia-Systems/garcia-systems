<x-layouts.app title="Tools" page-description="Use Garcia Systems tools including the AI Readiness Assessment and Opportunity Atlas.">
    <section class="mx-auto max-w-6xl px-6 py-16 md:py-20">
        <x-section-heading eyebrow="Tools" title="Practical starting points for workflow and AI readiness conversations." description="Use these resources to connect operating friction with better questions, clearer risks, and focused next steps." />

        <div class="mt-8 grid gap-5 md:grid-cols-2">
            @if(config('garcia.features.ai_assessment'))
                <x-feature-card title="AI Readiness Assessment" description="Score workflow clarity, data quality, ownership, and risk before selecting an AI or automation pilot." :href="route('assessment')" linkText="Open tool" />
            @endif
            @if(config('garcia.features.opportunity_atlas'))
                <x-feature-card title="Opportunity Atlas" description="Browse common industries, workflows, friction points, and solution patterns for modernization work." :href="route('atlas')" linkText="Explore" />
            @endif
            @unless(config('garcia.features.ai_assessment') || config('garcia.features.opportunity_atlas'))
                <x-card><p class="text-slate-300">Public tools are temporarily unavailable while Garcia Systems reviews the best way to present them.</p></x-card>
            @endunless
        </div>
    </section>
</x-layouts.app>
