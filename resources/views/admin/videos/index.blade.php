<x-admin.layout heading="Videos">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <a class="rounded bg-cyan-400 px-4 py-2 text-slate-950" href="{{ route('admin.videos.create') }}">Create video</a>
        <form method="get" action="{{ route('admin.videos.index') }}" class="flex gap-2">
            <input class="rounded px-3 py-2 text-slate-900" name="search" value="{{ $search }}" placeholder="Search videos">
            <button class="rounded border border-slate-600 px-4 py-2">Search</button>
        </form>
    </div>
    <div class="mt-6 space-y-3">@foreach($videos as $video)<div class="rounded border border-slate-700 p-4"><div class="flex justify-between"><a class="font-semibold" href="{{ route('admin.videos.edit',$video) }}">{{ $video->title }}</a><span>{{ $video->is_published ? 'Published' : 'Draft' }}</span></div><p class="text-sm text-slate-400">{{ $video->slug }} · {{ $video->article?->title }}</p><form method="post" action="{{ route('admin.videos.publish',$video) }}">@csrf @method('patch')<button class="text-cyan-300">{{ $video->is_published ? 'Unpublish' : 'Publish' }}</button></form></div>@endforeach</div>{{ $videos->links() }}
</x-admin.layout>
