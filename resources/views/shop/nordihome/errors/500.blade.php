@extends('shop.nordihome.layouts.blank')
@section('title', 'Страница 500')
@section('main', 'error')
@section('content')

    <div class="container-xl mt-5">
        <h1>Страница не найдена. Ошибка 500</h1>
        <div class="fs-5">
            Какая-то ошибка, мы сами вообще не вкурсе что сломалось.
        </div>
        <div class="fs-5">
            Но, в любом случае мы все починим. Спасибо за внимание
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
