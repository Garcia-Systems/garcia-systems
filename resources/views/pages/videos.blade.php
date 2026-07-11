<x-layouts.app title="Videos" page-description="Watch Garcia Systems videos about workflow modernization, systems consulting, automation, and AI readiness.">
    <section class="mx-auto max-w-6xl px-6 py-16 md:py-20">
        <x-section-heading eyebrow="Videos" title="Short explainers for workflow, automation, and AI readiness decisions." description="Use these videos to frame common operating problems and practical technology choices before committing to heavier implementation work." />

        <div class="mt-8 grid gap-5 md:grid-cols-3">
            @forelse($videos as $video)
                <x-card class="overflow-hidden p-0">
                    @if($video->embed_url)
                        <div class="video-embed rounded-b-none">
                            <iframe src="{{ $video->embed_url }}" title="{{ $video->title }} video player" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        </div>
                    @elseif($video->display_thumbnail_url)
                        <img class="h-40 w-full object-cover" src="{{ $video->display_thumbnail_url }}" alt="Thumbnail for {{ $video->title }}">
                    @endif
                    <div class="p-6">
                        <h2 class="text-xl font-semibold">{{ $video->title }}</h2>
                        <p class="mt-3 text-slate-300">{{ $video->description }}</p>
                        <a class="mt-5 inline-flex font-semibold text-cyan-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300" href="{{ $video->watch_url }}" target="_blank" rel="noopener noreferrer">Watch on YouTube →</a>
                    </div>
                </x-card>
            @empty
                <x-card class="md:col-span-3">
                    <h2 class="text-xl font-semibold">Video library is coming soon.</h2>
                    <p class="mt-3 text-slate-300">Expect concise walkthroughs focused on decision-making, workflows, and practical AI use cases.</p>
                </x-card>
            @endforelse
        </div>
    </section>
</x-layouts.app>
