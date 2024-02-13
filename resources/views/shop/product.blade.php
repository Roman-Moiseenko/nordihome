@extends('layouts.shop')

@section('body', 'product')
@section('main', 'container-xl product-page')
@section('title', $title)
@section('description', $description)

@section('content')

    <div class="title-page">
        <h1>{{ $product->name }}</h1>
    </div>

    <div class="box-card">
        <div class="row">
            <div class="col-lg-6">
                <div class="view-image-product">
                    <img id="main-image-product" src="{{ $product->photo->getThumbUrl('card') }}" style="width: 100%;">
                </div>

                <div class="slider-images-product owl-carousel owl-theme mt-3 p-3" data-responsive="[3,6,9]">
                    @foreach($product->photos as $photo)
                        <img src="{{ $photo->getThumbUrl('mini') }}" data-image="{{ $photo->getThumbUrl('card') }}"
                             class="slider-image-product" alt="{{ $photo->alt }}">
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
                            <a href="#reviews" title="Отзывы реальных покупателей"
                               aria-label="Отзывы реальных покупателей">{{ $product->countReviews() }}</a>
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
                        <div class="count-product">
                            В наличии {{ $product->count_for_sell }} шт.
                        </div>
                    </div>
                    <div class="product-card-to-cart">
                        <button class="to-cart btn btn-dark" data-product="{{ $product->id }}">В Корзину</button>
                        <button class="one-click btn btn-outline-dark" data-product="{{ $product->id }}">В 1 Клик!
                        </button>
                    </div>
                    @if(!is_null($product->modification()))
                        <div class="view-modification">
                            @foreach($product->modification->products as $_product)
                                <div>
                                    @if($product->id == $_product->id)
                                        <img src="{{ $product->photo->getThumbUrl('thumb') }}"
                                             alt="{{ $_product->photo->alt }}">
                                    @else
                                        <a href="{{ route('shop.product.view', $_product->slug) }}"
                                           title="{{ $_product->name }}">
                                            <img src="{{ $_product->photo->getThumbUrl('thumb') }}"
                                                 alt="{{ $_product->photo->alt }}">
                                        </a>
                                    @endif
                                </div>
                            @endforeach

                        </div>
                    @endif
                    @if(!empty($product->related))
                        <div class="view-related">
                            <h4>Аксессуары</h4>
                            <div class="slider-images-product owl-carousel owl-theme" data-responsive="[2,4,6]">
                                @foreach($product->related as $_product)
                                    <a href="{{ route('shop.product.view', $_product->slug) }}"
                                       title="{{ $_product->name }}">
                                        <img src="{{ $_product->photo->getThumbUrl('thumb') }}"
                                             alt="{{ $_product->photo->alt }}">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div class="view-specifications">
                    Базовые характеристики + ссылка -
                    <a href="#specifications">Все характеристики</a>
                </div>
            </div>
        </div>
    </div>

    </div>

    @if($product->bonus->count() > 0)
        <div class="box-card view-bonus">
            <h3 id="bonus">Выгодная покупка</h3>
            <div class="d-flex justify-content-around">
            @foreach($product->bonus as $_product)
                <div class="px-2">
                    <img src="{{ $_product->photo->getThumbUrl('thumb') }}" alt="{{ $_product->photo->alt }}">
                    <div class="price-block">
                        <span class="discount-price">{{ price($_product->pivot->discount) }}</span>
                        <span class="base-price">{{ price($_product->lastPrice->value) }}</span>
                    </div>
                    <button class="to-cart btn btn-dark" data-product="{{ $_product->id }}">В Корзину</button>
                </div>
            @endforeach
            </div>
            <div class="fs-8 mt-3">
                * Бонусная покупка работает при условии одинакового количества основного и бонусного товара в корзине.
            </div>
        </div>
    @endif

    @if(!empty($product->series))
        <div class="box-card">
            <h3 id="series">Все товары серии {{ $product->series->name }}</h3>
            <div class="slider-images-product owl-carousel owl-theme" data-responsive="[3,6,9]">
                @foreach($product->series->products as $_product)
                    <div class="px-1">
                        <a href="{{ route('shop.product.view', $_product->slug) }}" title="{{ $_product->name }}">
                            <img src="{{ $_product->photo->getThumbUrl('thumb') }}" alt="{{ $_product->photo->alt }}">
                        </a>
                        <a href="{{ route('shop.product.view', $_product->slug) }}" title="{{ $_product->name }}">
                            <span class="fs-8 product-trunc">
                                {{ $_product->name }}
                            </span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    <div class="box-card">
        <h3 id="description">Описание</h3>
        {!! $product->description !!}
    </div>

    <div class="box-card">
        <h3 id="specifications">Характеристики</h3>
        Характеристики
    </div>

    <div>
        Аналоги (из группы Эквиваленты)
    </div>
    <div id="reviews" class="box-card">
        Отзывы
    </div>

    <div>
        Вы смотрели ???
    </div>

    {!! $schema->ProductPage($product) !!}
@endsection
