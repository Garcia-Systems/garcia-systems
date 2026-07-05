<x-layouts.app :title="$title ?? 'Admin'">
<section class="mx-auto max-w-6xl px-6 py-10">
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4"><div><p class="text-sm uppercase tracking-wide text-cyan-300">Garcia Systems CMS</p><h1 class="text-3xl font-bold">{{ $heading ?? 'Admin' }}</h1></div><nav class="flex gap-3 text-sm"><a href="{{ route('admin.articles.index') }}">Articles</a><a href="{{ route('admin.videos.index') }}">Videos</a><a href="{{ route('admin.categories.index') }}">Categories</a><a href="{{ route('admin.tags.index') }}">Tags</a></nav></div>
    @if(session('status'))<div class="mb-6 rounded bg-emerald-500/20 p-3 text-emerald-100">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="mb-6 rounded bg-red-500/20 p-3 text-red-100"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    {{ $slot }}
</section>
</x-layouts.app>
