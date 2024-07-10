@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование скидки
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.discount.discount.update', $discount) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.discount.discount._fields-form', ['discount' => $discount])
    </form>

@endsection
