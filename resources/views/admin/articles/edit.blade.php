<x-admin.layout heading="Edit article">
    <form method="post" action="{{ route('admin.articles.update',$article) }}">@method('put') @include('admin.articles._form')</form>
    <form class="mt-6" method="post" action="{{ route('admin.articles.destroy',$article) }}">
        @csrf @method('delete')
        <button class="rounded border border-amber-300 px-4 py-2 text-amber-300" onclick="return confirm('Archive this article? It will be hidden from public pages.')">Archive article</button>
    </form>
</x-admin.layout>
