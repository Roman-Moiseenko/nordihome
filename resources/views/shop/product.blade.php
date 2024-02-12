@extends('layouts.shop')

@section('body', 'product')
@section('main', 'container-xl product-page')
@section('title', $title)
@section('description', $description)

@section('content')

    <div class="title-page">
        {{ $product->name }}
    </div>

    <div class="row">
        <div class="col-lg-6" style="background: #f0f0f0">
            <div class="">
                <div>
                    <img src="{{ $product->photo->getThumbUrl('card') }}" style="width: 100%;">
                </div>
                <div class="d-flex">
                    @foreach($product->photos as $photo)
                        <img src="{{ $photo->getThumbUrl('mini') }}" style="width: 100%">
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
