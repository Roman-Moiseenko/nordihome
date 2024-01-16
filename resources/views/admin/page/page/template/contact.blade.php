@extends('layouts.shop')

@section('main')
    pages
@endsection

@section('content')

    <div class="container-xl">
        Контакты
    </div>
    <div class="mt-3">
        @include('shop.widgets.map')
    </div>
@endsection
