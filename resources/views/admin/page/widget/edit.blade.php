@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Редактировать виджет
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.page.widget.update', $widget) }}" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        @include('admin.page.widget._fields-form', ['widget' => $widget])
    </form>

@endsection
