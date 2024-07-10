@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование атрибута
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.product.attribute.update', $attribute) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.product.attribute._fields-form', ['attribute' => $attribute])
    </form>
@endsection
