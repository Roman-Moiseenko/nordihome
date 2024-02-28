@extends('layouts.shop')
@section('title', 'Страница 404')
@section('main', 'error')
@section('content')

    <div class="container-xl">
        <h1></h1>
        <div class="fs-5">
            Вы можете выбрать товар из наличия в нашем каталоге или заказать товары Икеа на заказ с доставкой
        </div>
    </div>

    <div class="container-xl my-5 nalich-i-zakaz">
        <div class="row">
            <div class="col-lg-6" style="">
                <a href="{{ route('shop.category.index') }}" class="c-mini-item">
                    <img src="/images/pages/home/t-nalichie-min.jpg" alt="Товары Икеа в наличии">
                    <div class="heading">Товары в наличии</div>
                </a>
            </div>
            <div class="col-lg-6" style="">
                <a href="{{ route('shop.parser.view') }}" class="c-mini-item">
                    <img src="/images/pages/home/t-zakaz-min.jpg" alt="Товары Икеа под заказ">
                    <div class="heading">Товары под заказ</div>
                </a>
            </div>
        </div>
    </div>
@endsection
