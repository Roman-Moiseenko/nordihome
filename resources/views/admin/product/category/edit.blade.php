@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование категории
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.product.category.update', $category) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.product.category._fields-form', ['category' => $category, 'categories' => $categories])
    </form>
@endsection
