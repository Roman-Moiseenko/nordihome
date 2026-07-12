@php
    /** @var \App\Modules\Shop\Application\DTOs\Pages\ProductViewPageData $pageData */
    $product = $pageData->product;
@endphp

@extends('shop.layouts.main')
@section('body', 'product')
@section('main', 'container-xl product-page')
@section('title', $pageData->meta->title)
@section('description', $pageData->meta->description)

@section('content')
    <span class="e-detail" data-product="{{ $product->id }}"></span>

    <div class="title-page">
        <h1>{{ $product->name }}</h1>
    </div>

    <div class="box-card">
        <div class="row">
            <div class="col-lg-6">
                <div class="view-image-product">
                    @if(!empty($product->images))
                        <img id="main-image-product" src="{{ $product->images[0]->src }}" style="width: 100%;">
                    @endif
                </div>

                <div class="slider-images-product owl-carousel owl-theme mt-3 p-3" data-responsive="[3,6,9]">
                    @foreach($product->images as $photo)
                        <img src="{{ $photo->mini }}" data-image="{{ $photo->src }}"
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
                               aria-label="Отзывы реальных покупателей">{{ $product->count_reviews }}</a>
                            @include('shop.widgets.stars', ['rating' => $product->rating, 'show_number' => true])
                        </div>
                    </div>
                    <div class="price-brand-block">
                        <div class="view-price">
                            @if($product->is_sale)
                                @if(!$product->promotion->has)
                                    @if($product->price_previous > $product->price)
                                        <div class="comment">* Цена на товар снижена</div>
                                        <span class="discount-price">{{ price($product->price) }}</span>
                                        <span class="base-price">{{ price($product->price_previous) }}</span>
                                    @else
                                        {{ price($product->price) }}
                                    @endif
                                @else
                                    <div class="comment">* Цена по акции {{ $product->promotion->title }}</div>
                                    <span class="discount-price">{{ price($product->promotion->price) }}</span>
                                    <span class="base-price">{{ price($product->price) }}</span>
                                @endif
                            @else
                                {{ price($product->price) }}
                            @endif
                            <div class="count-product">
                                @if($product->is_sale)
                                    @if($product->quantity > 0)
                                        Товар в наличии
                                    @else
                                        Только под заказ
                                    @endif
                                @endif
                                <span class="" style="color: red; font-weight: bold;">Товар не продается! Цена вымышленная!</span>
                            </div>
                        </div>
                        <div class="view-brand">
                            @if(empty($product->brandLogo))
                                <span>{{ $product->brandName }}</span>
                            @else
                                <img src="{{ $product->brandLogo }}"
                                     alt="{{ $product->brandName  }}" title="{{ $product->brandName }}">
                            @endif

                        </div>
                    </div>
                    <div class="product-card-to-cart">
                        @if($product->is_sale)
                            <button class="to-cart btn btn-dark e-add" data-product="{{ $product->id }}">В Корзину
                            </button>


                            <button class="one-click btn btn-outline-dark"
                                    type="button" data-bs-toggle="modal"
                                    data-bs-target="#buy-click"
                                    onclick="document.getElementById('one-click-product-id').value={{$product->id}};
                                    document.getElementById('button-buy-click').setAttribute('data-product', {{$product->id}});"
                            >В 1 Клик!
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary" disabled>Снят с продажи</button>
                        @endif
                    </div>

                    @include('shop.product.__modification', ['modification' => $pageData->modification, 'current_id' => $product->id])
                    @include('shop.product.__related', ['related' => $pageData->related])

                    <div class="view-specifications">
                        @include('shop.widgets.dimensions',
                                ['dimensions' => $product->dimensions,
                                'isRegion' => $product->isRegion,
                                'isDelivery' => $product->isDelivery])
                    </div>
                    <div style="color: #ff5555">САЙТ В РАЗРАБОТКЕ! Оригинальный сайт по адресу <a
                            href="https://nordihome.ru/" style="color: #5555ff">https://nordihome.ru/</a></div>
                </div>
            </div>
        </div>
        <div class="view-footer-product">
            @if(!is_null($pageData->attributes))
                <div class="anchor-menu"><a href="#specifications">Характеристики</a></div>
            @endif
            <div class="anchor-menu"><a href="#description">Описание товара</a></div>
            <div class="product-code">Артикул <span>{{ $product->code }}</span></div>

        </div>
    </div>

    @include('shop.product._bonus', ['bonus' => $pageData->bonus])
    @include('shop.product._series', ['series' => $pageData->series])
    <div class="box-card">
        <h3 id="description">Описание</h3>
        {!! $product->description !!}
    </div>
    @include('shop.product._attribute', ['productAttributes' => $pageData->attributes])
    @include('shop.product._equivalent', ['equivalents' => $pageData->equivalents])
    @include('shop.product._reviews', ['reviews' => $pageData->reviews])

    <script type="application/ld+json" class="schemantra.com">
        {!! json_encode($pageData->schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection
