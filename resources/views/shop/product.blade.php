@extends('layouts.shop')

@section('body', 'product')
@section('main', 'container-xl product-page')
@section('title', $title)
@section('description', $description)

@section('content')

    <div class="title-page">
        <h1>{{ $product->name }}</h1>
    </div>

    <div class="box-card row">
        <div class="col-lg-6">
            <div class="" style="padding: 10px 40px;">
                <img id="main-image-product" src="{{ $product->photo->getThumbUrl('card') }}" style="width: 100%;">
            </div>

            <div id="slider-images-product" class="owl-carousel owl-theme mt-3 p-3">
                @foreach($product->photos as $photo)
                    <img src="{{ $photo->getThumbUrl('mini') }}" data-image="{{ $photo->getThumbUrl('card') }}" class="slider-image-product">
                @endforeach
            </div>

        </div>
        Изображения + Видео
    </div>
    <div class="col-lg-6" style="background: #e0e0e0">
        Базовые характеристики. Модификации, Цена, В Корзину, Аксессуары, В избранное, сравнить,
        В наличии. Акция
        <br>Якоря на Отзывы, Характеристики, Описание
    </div>
    </div>
    <div>
        Бонусный товар
    </div>

    <div>
        Все товары серии
    </div>

    <div>
        Описание
    </div>

    <div>
        Характеристики
    </div>

    <div>
        Аналоги (из группы Эквиваленты)
    </div>
    <div>
        Отзывы
    </div>

    <div>
        Вы смотрели ???
    </div>



    {!! $schema->ProductPage($product) !!}
@endsection
