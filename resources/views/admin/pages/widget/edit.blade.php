@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактировать виджет
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.pages.widget.update', $widget) }}" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        @include('admin.pages.widget._fields-form', ['widget' => $widget])
    </form>

@endsection
