<x-admin.layout :title="'Edit '.$config['singular']" :heading="'Atlas / Edit '.$config['singular']">
    @include('admin.atlas._form', ['action' => route('admin.atlas.update', [$resource, $item]), 'method' => 'PUT'])
    <form class="mt-6" method="POST" action="{{ route('admin.atlas.destroy', [$resource, $item]) }}" onsubmit="return confirm('Delete this record?');">@csrf @method('DELETE')<button class="rounded border border-red-300 px-4 py-2 text-red-200">Delete if safe</button></form>
</x-admin.layout>
