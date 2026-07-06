<x-admin.layout heading="Edit video">
    <form method="post" action="{{ route('admin.videos.update',$video) }}">@method('put') @include('admin.videos._form')</form>
    <form class="mt-6" method="post" action="{{ route('admin.videos.destroy',$video) }}">
        @csrf @method('delete')
        <button class="rounded border border-amber-300 px-4 py-2 text-amber-300" onclick="return confirm('Archive this video? It will be hidden from public pages.')">Archive video</button>
    </form>
</x-admin.layout>
