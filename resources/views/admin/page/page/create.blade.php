@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Создать страницу
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.page.page.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.page.page._fields-form', ['page' => null])
    </form>

@endsection
