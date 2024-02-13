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
            <div class="view-image-product">
                <img id="main-image-product" src="{{ $product->photo->getThumbUrl('card') }}" style="width: 100%;">
            </div>

            <div id="slider-images-product" class="owl-carousel owl-theme mt-3 p-3">
                @foreach($product->photos as $photo)
                    <img src="{{ $photo->getThumbUrl('mini') }}" data-image="{{ $photo->getThumbUrl('card') }}" class="slider-image-product">
                @endforeach
            </div>

        </div>
        <div class="col-lg-6">
            <div class="view-info-product">
                <div class="view-rating">
                    <div>
                        @include('shop.widgets.to-wish', ['product' => $product])
                    </div>
                    <div>
                        <a href="#review" title="Отзывы реальных покупателей" aria-label="Отзывы реальных покупателей">{{ $product->countReviews() }}</a>
                        @include('shop.widgets.stars', ['rating' => $product->current_rating])
                    </div>
                </div>
                <div class="view-price">
                    @if(is_null($product->isPromotion()))
                        {{ price($product->getLastPrice()) }}
                    @else
                        <span class="discount-price">{{ price($product->isPromotion()['price']) }}</span>
                        <span class="base-price">{{ price($product->lastPrice->value) }}</span>
                    @endif
                </div>
                <div class="product-card-to-cart">
                    <button class="to-cart btn btn-dark" data-product="{{ $product->id }}">В Корзину</button>
                    <button class="one-click btn btn-outline-dark" data-product="{{ $product->id }}">В 1 Клик!</button>
                </div>
                <div>
                    Модификации
                </div>
                <div>
                    Аксессуары
                </div>
            </div>
            Базовые характеристики. , , , , сравнить,
            В наличии.
            <br>Якоря на Отзывы, Характеристики, Описание
        </div>
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
