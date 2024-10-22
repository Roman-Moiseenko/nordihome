@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Создание акции
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.discount.promotion.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.discount.promotion._fields-form', ['promotion' => null])
    </form>

@endsection
