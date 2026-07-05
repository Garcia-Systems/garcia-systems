<x-admin.layout heading="Edit article"><form method="post" action="{{ route('admin.articles.update',$article) }}">@method('put') @include('admin.articles._form')</form></x-admin.layout>
