<x-admin.layout heading="Videos">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-wrap gap-3">
            <a class="rounded bg-cyan-400 px-4 py-2 text-slate-950" href="{{ route('admin.videos.create') }}">Create video</a>
            <a class="rounded border border-slate-600 px-4 py-2 {{ $status === 'active' ? 'bg-slate-800' : '' }}" href="{{ route('admin.videos.index', ['search' => $search]) }}">Active</a>
            <a class="rounded border border-slate-600 px-4 py-2 {{ $status === 'archived' ? 'bg-slate-800' : '' }}" href="{{ route('admin.videos.index', ['status' => 'archived', 'search' => $search]) }}">Archived</a>
        </div>
        <form method="get" action="{{ route('admin.videos.index') }}" class="flex gap-2">
            @if($status === 'archived')<input type="hidden" name="status" value="archived">@endif
            <input class="rounded px-3 py-2 text-slate-900" name="search" value="{{ $search }}" placeholder="Search videos">
            <button class="rounded border border-slate-600 px-4 py-2">Search</button>
        </form>
    </div>
    <div class="mt-6 space-y-3">@foreach($videos as $video)<div class="rounded border border-slate-700 p-4"><div class="flex justify-between"><div class="font-semibold">@if($video->trashed()){{ $video->title }}@else<a href="{{ route('admin.videos.edit',$video) }}">{{ $video->title }}</a>@endif</div><span>{{ $video->trashed() ? 'Archived' : ($video->is_published ? 'Published' : 'Draft') }}</span></div><p class="text-sm text-slate-400">{{ $video->slug }} · {{ $video->article?->title }}</p><div class="mt-3 flex flex-wrap gap-4">@if($video->trashed())<form method="post" action="{{ route('admin.videos.restore',$video->id) }}">@csrf @method('patch')<button class="text-cyan-300" onclick="return confirm('Restore this archived video?')">Restore</button></form>@else<form method="post" action="{{ route('admin.videos.publish',$video) }}">@csrf @method('patch')<button class="text-cyan-300">{{ $video->is_published ? 'Unpublish' : 'Publish' }}</button></form><form method="post" action="{{ route('admin.videos.destroy',$video) }}">@csrf @method('delete')<button class="text-amber-300" onclick="return confirm('Archive this video? It will be hidden from public pages.')">Archive</button></form>@endif</div></div>@endforeach</div>{{ $videos->links() }}
</x-admin.layout>
