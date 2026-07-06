<x-admin.layout heading="Articles">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-wrap gap-3">
            <a class="rounded bg-cyan-400 px-4 py-2 text-slate-950" href="{{ route('admin.articles.create') }}">Create article</a>
            <a class="rounded border border-slate-600 px-4 py-2 {{ $status === 'active' ? 'bg-slate-800' : '' }}" href="{{ route('admin.articles.index', ['search' => $search]) }}">Active</a>
            <a class="rounded border border-slate-600 px-4 py-2 {{ $status === 'archived' ? 'bg-slate-800' : '' }}" href="{{ route('admin.articles.index', ['status' => 'archived', 'search' => $search]) }}">Archived</a>
        </div>
        <form method="get" action="{{ route('admin.articles.index') }}" class="flex gap-2">
            @if($status === 'archived')<input type="hidden" name="status" value="archived">@endif
            <input class="rounded px-3 py-2 text-slate-900" name="search" value="{{ $search }}" placeholder="Search articles">
            <button class="rounded border border-slate-600 px-4 py-2">Search</button>
        </form>
    </div>
    <div class="mt-6 space-y-3">
        @foreach($articles as $article)
            <div class="rounded border border-slate-700 p-4">
                <div class="flex justify-between"><div class="font-semibold">@if($article->trashed()){{ $article->title }}@else<a href="{{ route('admin.articles.edit',$article) }}">{{ $article->title }}</a>@endif</div><span>{{ $article->trashed() ? 'Archived' : ($article->is_published ? 'Published' : 'Draft') }}</span></div>
                <p class="text-sm text-slate-400">{{ $article->slug }} · {{ $article->category?->name }}</p>
                <div class="mt-3 flex flex-wrap gap-4">
                    @if($article->trashed())
                        <form method="post" action="{{ route('admin.articles.restore',$article->id) }}">@csrf @method('patch')<button class="text-cyan-300" onclick="return confirm('Restore this archived article?')">Restore</button></form>
                    @else
                        <form method="post" action="{{ route('admin.articles.publish',$article) }}">@csrf @method('patch')<button class="text-cyan-300">{{ $article->is_published ? 'Unpublish' : 'Publish' }}</button></form>
                        <form method="post" action="{{ route('admin.articles.destroy',$article) }}">@csrf @method('delete')<button class="text-amber-300" onclick="return confirm('Archive this article? It will be hidden from public pages.')">Archive</button></form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>{{ $articles->links() }}
</x-admin.layout>
