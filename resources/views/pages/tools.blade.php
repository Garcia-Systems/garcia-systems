<x-layouts.app title="Tools" page-description="Use Garcia Systems tools including the AI Readiness Assessment and Opportunity Atlas.">
    <section class="mx-auto max-w-6xl px-6 py-16 md:py-20">
        <x-section-heading eyebrow="Tools" title="Practical starting points for workflow and AI readiness conversations." description="Use these resources to connect operating friction with better questions, clearer risks, and focused next steps." />

        <div class="mt-8 grid gap-5 md:grid-cols-2">
            <x-feature-card title="AI Readiness Assessment" description="Score workflow clarity, data quality, ownership, and risk before selecting an AI or automation pilot." :href="route('assessment')" linkText="Open tool" />
            <x-feature-card title="Opportunity Atlas" description="Browse common industries, workflows, friction points, and solution patterns for modernization work." :href="route('atlas')" linkText="Explore" />
        </div>
    </section>
</x-layouts.app>
