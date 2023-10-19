@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Добавление корневой категории
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.product.category.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="parent">
        @include('admin.product.category._fields-form')
    </form>

@endsection
