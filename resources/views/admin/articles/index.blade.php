<x-admin.layout heading="Articles">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <a class="rounded bg-cyan-400 px-4 py-2 text-slate-950" href="{{ route('admin.articles.create') }}">Create article</a>
        <form method="get" action="{{ route('admin.articles.index') }}" class="flex gap-2">
            <input class="rounded px-3 py-2 text-slate-900" name="search" value="{{ $search }}" placeholder="Search articles">
            <button class="rounded border border-slate-600 px-4 py-2">Search</button>
        </form>
    </div>
    <div class="mt-6 space-y-3">@foreach($articles as $article)<div class="rounded border border-slate-700 p-4"><div class="flex justify-between"><a class="font-semibold" href="{{ route('admin.articles.edit',$article) }}">{{ $article->title }}</a><span>{{ $article->is_published ? 'Published' : 'Draft' }}</span></div><p class="text-sm text-slate-400">{{ $article->slug }} · {{ $article->category?->name }}</p><form method="post" action="{{ route('admin.articles.publish',$article) }}">@csrf @method('patch')<button class="text-cyan-300">{{ $article->is_published ? 'Unpublish' : 'Publish' }}</button></form></div>@endforeach</div>{{ $articles->links() }}
</x-admin.layout>
