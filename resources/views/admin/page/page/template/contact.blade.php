@extends('layouts.shop')

@section('main')
    pages
@endsection

@section('content')

    <div class="container-xl">
        Блок о нас
    </div>

    <div class="container-xl">
        <div class="row">
            <div class="col-lg-6">
                Контакты
            </div>
            <div class="col-lg-6">
                Форма заявки
            </div>
        </div>
    </div>

    <div class="mt-3">
        @include('shop.widgets.map')
    </div>
@endsection
