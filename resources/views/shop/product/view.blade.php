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
                    <div class="price-brand-block">
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
                        <div class="view-brand">
                            @if(empty($product->brand->photo))
                                <span>{{ $product->brand->name }}</span>
                            @else
                                <img src="{{ $product->brand->photo->getUploadUrl() }}" alt="{{ $product->brand->name }}" title="{{ $product->brand->name }}">
                            @endif

                        </div>
                    </div>
                    <div class="product-card-to-cart">
                        <button class="to-cart btn btn-dark" data-product="{{ $product->id }}">В Корзину</button>
                        <button class="one-click btn btn-outline-dark"
                                data-product="{{ $product->id }}" type="button" data-bs-toggle="modal" data-bs-target="#buy-click"
                                onclick="document.getElementById('one-click-product-id').value={{$product->id}};"
                        >В 1 Клик!
                        </button>
                    </div>

                    @include('shop.product.__modification', ['modification' => $product->modification, 'current_id' => $product->id])
                    @include('shop.product.__related', ['related' => $product->related])

                    <div class="view-specifications">
                        @include('shop.widgets.dimensions', ['dimensions' => $product->dimensions, 'local' => !$product->not_local, 'region' => !$product->not_delivery])
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 d-flex">
                @if(!empty($productAttributes))
                <div class="anchor-menu"><a href="#specifications">Характеристики</a></div>
                @endif
                <div class="anchor-menu"><a href="#description">Описание товара</a></div>
            </div>
        </div>
    </div>

    @include('shop.product._bonus', ['bonus' => $product->bonus])

    @include('shop.product._series', ['series' => $product->series])

    <div class="box-card">
        <h3 id="description">Описание</h3>
        {!! $product->description !!}
    </div>

    @include('shop.product._attribute', ['productAttributes' => $productAttributes])


    @include('shop.product._equivalent', ['equivalent' => $product->equivalent])
    @include('shop.product._reviews', ['reviews' => $product->reviews])

    {!! $schema->ProductPage($product) !!}
@endsection
