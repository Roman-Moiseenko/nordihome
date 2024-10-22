@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование акции
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.discount.promotion.update', $promotion) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.discount.promotion._fields-form', ['promotion' => $promotion])
    </form>
@endsection
