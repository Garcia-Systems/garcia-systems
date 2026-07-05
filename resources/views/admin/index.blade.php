<x-admin.layout heading="Admin">
    <div class="grid gap-4 md:grid-cols-5">
        @foreach($metrics as $label => $count)
            <div class="rounded-xl border border-slate-700 bg-slate-900 p-5">
                <p class="text-sm text-slate-400">{{ $label }}</p>
                <p class="mt-2 text-3xl font-bold text-white">{{ $count }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-4 md:grid-cols-4">
        @foreach(['articles' => 'Articles', 'videos' => 'Videos', 'categories' => 'Categories', 'tags' => 'Tags'] as $route => $label)
            <a class="rounded-xl border border-slate-700 bg-slate-900 p-6 font-semibold" href="{{ route('admin.'.$route.'.index') }}">Manage {{ $label }}</a>
        @endforeach
    </div>

    <div class="mt-10 grid gap-6 lg:grid-cols-3">
        <section class="rounded-xl border border-slate-700 bg-slate-900 p-5">
            <h2 class="font-semibold text-white">Recent articles</h2>
            <div class="mt-4 space-y-3">
                @forelse($recentArticles as $article)
                    <a class="block text-sm text-cyan-300" href="{{ route('admin.articles.edit', $article) }}">{{ $article->title }}</a>
                @empty
                    <p class="text-sm text-slate-400">No articles yet.</p>
                @endforelse
            </div>
        </section>
        <section class="rounded-xl border border-slate-700 bg-slate-900 p-5">
            <h2 class="font-semibold text-white">Recent assessments</h2>
            <div class="mt-4 space-y-3">
                @forelse($recentAssessments as $assessment)
                    <p class="text-sm text-slate-300">{{ $assessment->company ?: $assessment->email ?: 'Anonymous assessment' }} · {{ $assessment->score }}</p>
                @empty
                    <p class="text-sm text-slate-400">No assessments yet.</p>
                @endforelse
            </div>
        </section>
        <section class="rounded-xl border border-slate-700 bg-slate-900 p-5">
            <h2 class="font-semibold text-white">Recent contact submissions</h2>
            <div class="mt-4 space-y-3">
                @forelse($recentContactSubmissions as $submission)
                    <p class="text-sm text-slate-300">{{ $submission->name }} · {{ $submission->email }}</p>
                @empty
                    <p class="text-sm text-slate-400">No contact submissions yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-admin.layout>
