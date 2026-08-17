<!--template:Страница О компании-->
@extends('layouts.main')

@section('main')
    pages
@endsection

@section('title', $title)
@section('description', $description)

@section('content')
    <div class="container-xl">
        <h1 class="my-4">{{ $page->name }}</h1>
        <div class="row">
            <div class="col-lg-6 bg-black b-radius_12 m-b_10">
                <div class="about-block-text"><p>НОРДИ ХОУМ — магазин мебели и товаров для дома из Европы с доставкой по всей России.</p>
                <p>С 2020 года мы привозим оригинальные товары ИКЕА, Zara Home и других европейских брендов, а также сотрудничаем с мебельными фабриками Калининграда.
                    В нашем ассортименте — более 10 000 товаров в наличии: мебель, посуда, декор, текстиль, товары для хранения и другие решения для обустройства дома. Если нужной позиции нет в каталоге, мы можем найти и привезти её из Европы специально под заказ.</p>
                <p><b>Наш магазин находится в Калининграде, откуда мы отправляем заказы в разные регионы России.</b></p>
                <p>Начните знакомство с НОРДИ ХОУМ с нашего каталога — здесь собраны товары для самых разных интерьеров.</p>
                <a href="/catalog/" class="btn btn-white f-z_14">ПЕРЕЙТИ В КАТАЛОГ</a>
                </div>
            </div>
            <div class="col-lg-6 m-b_10"><img src="/images/pages/about/bg-sl-12.jpg" alt="о компании Норди Хоум" class="b-radius_12"></div>
        </div>
    </div>
    <div class="container-xl">
        <div class="mt-4">
            {!! $page->text !!}
        </div>
    </div>
@endsection
