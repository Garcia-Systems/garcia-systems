<x-admin.layout :title="'New '.$config['singular']" :heading="'Atlas / New '.$config['singular']">
    @include('admin.atlas._form', ['action' => route('admin.atlas.store', $resource), 'method' => 'POST'])
</x-admin.layout>
