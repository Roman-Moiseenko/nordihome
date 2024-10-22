@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Добавить виджет
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.page.widget.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.page.widget._fields-form', ['widget' => null])
    </form>

@endsection
