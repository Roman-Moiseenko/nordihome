@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Добавление подкаталога
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.product.category.store') }}" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="parent_id" value="{{ $category->id }}">
        @include('admin.product.category._fields-form', ['category' => null])
    </form>

@endsection
