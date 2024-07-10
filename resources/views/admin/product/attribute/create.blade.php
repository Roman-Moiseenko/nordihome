@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Создание нового атрибута
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.product.attribute.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="parent_id">
        @include('admin.product.attribute._fields-form', ['attribute' => null])
    </form>

@endsection
