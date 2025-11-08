<!--template:О компании-->
@extends('shop.nordihome.layouts.main')

@section('main')
    pages
@endsection

@section('title', $title)
@section('description', $description)

@section('content')
    <div class="container-xl">
        <h1 class="my-4">{{ $page->name }}</h1>
        <div class="row">
            <div class="col-lg-6 bg-black">
                <div class="about-block-text"><p>NORDI HOME — магазин мебели и товаров из ИКЕА и других европейских магазинов с доставкой по всей РФ.</p>
                <p>Мы работаем с 2020 года и помогли уже более 10 000 клиентам преобразить их дома.</p>
                <p>Ниже можно ознакомиться более подробно с условиями и тарифами на доставку.</p>
                <p>Также в <a href="/shop/">нашем каталоге</a> Вы можете выбрать и купить мебель ИКЕА уже сегодня!</p>
                <a href="/shop/" class="btn btn-white">ПЕРЕЙТИ В КАТАЛОГ</a>
                </div>
            </div>
            <div class="col-lg-6"><img src="/images/pages/about/bg-sl-12.jpg" alt="о компании Nordi home"></div>
        </div>
    </div>
    <div class="container-xl">
        <div class="mt-4">
            {!! $page->text !!}
        </div>

        @include('shop.nordihome.widgets.map')
    </div>
@endsection
