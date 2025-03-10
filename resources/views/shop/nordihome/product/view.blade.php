@extends('shop.nordihome.layouts.main')

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
                    @if(!is_null($product->gallery))
                        <img id="main-image-product" src="{{ $product->getImage('card') }}" style="width: 100%;">
                    @endif
                </div>

                <div class="slider-images-product owl-carousel owl-theme mt-3 p-3" data-responsive="[3,6,9]">
                    @foreach($product->gallery as $photo)
                        <img src="{{ $photo->getThumbUrl('mini') }}" data-image="{{ $photo->getThumbUrl('card') }}"
                             class="slider-image-product" alt="{{ $photo->alt }}">
                    @endforeach
                </div>

            </div>
            <div class="col-lg-6">
                <div class="view-info-product">
                    <div class="view-rating">
                        <div>
                            @include('shop.nordihome.widgets.to-wish', ['product' => $product])
                        </div>
                        <div>
                            <a href="#reviews" title="Отзывы реальных покупателей"
                               aria-label="Отзывы реальных покупателей">{{ $product->countReviews() }}</a>
                            @include('shop.nordihome.widgets.stars', ['rating' => $product->current_rating, 'show_number' => true])
                        </div>
                    </div>
                    <div class="price-brand-block">
                        <div class="view-price">
                            @if($product->isSale())
                                @if(!$product->hasPromotion())
                                    @if($product->getPrice(true) > $product->getPrice())
                                        <div class="comment">* Цена на товар снижена</div>
                                        <span class="discount-price">{{ price($product->getPrice()) }}</span>
                                        <span class="base-price">{{ price($product->getPrice(true)) }}</span>
                                    @else
                                        {{ price($product->getPrice()) }}
                                    @endif
                                @else
                                    <div class="comment">* Цена по акции {{ $product->promotion()->title }}</div>
                                    <span class="discount-price">{{ price($product->promotion()->pivot->price) }}</span>
                                    <span class="base-price">{{ price($product->getPrice()) }}</span>
                                @endif
                            @else
                                {{ price($product->getPrice()) }}
                            @endif
                            <div class="count-product">
                                @if($product->isSale())
                                    @if($product->getQuantitySell() > 0)
                                        Товар в наличии
                                    @else
                                        Только под заказ
                                    @endif
                                @endif
                                <span class="" style="color: red; font-weight: bold;">Товар не продается! Цена вымышленная!</span>
                            </div>
                        </div>
                        <div class="view-brand">
                            @if(empty($product->brand->image))
                                <span>{{ $product->brand->name }}</span>
                            @else
                                <img src="{{ $product->brand->getImage() }}"
                                     alt="{{ $product->brand->name }}" title="{{ $product->brand->name }}">
                            @endif

                        </div>
                    </div>
                    <div class="product-card-to-cart">
                        @if($product->isSale())
                            <button class="to-cart btn btn-dark" data-product="{{ $product->id }}">В Корзину</button>
                            <button class="one-click btn btn-outline-dark"
                                    data-product="{{ $product->id }}" type="button" data-bs-toggle="modal"
                                    data-bs-target="#buy-click"
                                    onclick="document.getElementById('one-click-product-id').value={{$product->id}};"
                            >В 1 Клик!
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary" disabled>Снят с продажи</button>
                        @endif
                    </div>
                    @include('shop.nordihome.product.__modification', ['modification' => $product->modification, 'current_id' => $product->id])
                    @include('shop.nordihome.product.__related', ['related' => $product->related])

                    <div class="view-specifications">
                        @include('shop.nordihome.widgets.dimensions', ['dimensions' => $product->dimensions, 'local' => !$product->not_local, 'region' => !$product->not_delivery])
                    </div>
                    <div style="color: #ff5555">САЙТ В РАЗРАБОТКЕ! Оригинальный сайт по адресу <a href="https://nordihome.ru/" style="color: #5555ff">https://nordihome.ru/</a></div>
                </div>
            </div>
        </div>
        <div class="view-footer-product">
            @if(!empty($productAttributes))
                <div class="anchor-menu"><a href="#specifications">Характеристики</a></div>
            @endif
            <div class="anchor-menu"><a href="#description">Описание товара</a></div>
            <div class="product-code">Артикул <span>{{ $product->code }}</span></div>

        </div>
    </div>

    @include('shop.nordihome.product._bonus', ['bonus' => $product->bonus])

    @include('shop.nordihome.product._series', ['series' => $product->series])

    <div class="box-card">
        <h3 id="description">Описание</h3>
        {!! $product->description !!}
    </div>

    @include('shop.nordihome.product._attribute', ['productAttributes' => $productAttributes])
    @include('shop.nordihome.product._equivalent', ['equivalent' => $product->equivalent])
    @include('shop.nordihome.product._reviews', ['reviews' => $product->reviews])

    {!! $schema->ProductPage($product) !!}
@endsection
