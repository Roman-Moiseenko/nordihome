@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Добавить скидку
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.discount.discount.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.discount.discount._fields-form', ['discount' => null])
    </form>

@endsection
