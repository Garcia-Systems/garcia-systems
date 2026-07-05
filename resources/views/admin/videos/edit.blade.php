<x-admin.layout heading="Edit video"><form method="post" action="{{ route('admin.videos.update',$video) }}">@method('put') @include('admin.videos._form')</form></x-admin.layout>
