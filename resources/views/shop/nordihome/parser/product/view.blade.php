@extends('shop.nordihome.layouts.main')

@section('body', 'product')
@section('main', 'container-xl product-page')
@section('title', $title)
@section('description', $description)

@section('content')
    <span class="e-detail" data-product="{{ $product['id'] }}"></span>

    <div class="title-page">
        <h1>{{ $product['name'] }}</h1>
    </div>

    <div class="box-card">
        <div class="row">
            <div class="col-lg-6">
                <div class="view-image-product">
                    @if(!is_null($product['gallery']))
                        <img id="main-image-product" src="{{ $product['image']['src'] }}" style="width: 100%;">
                    @endif
                </div>

                <div class="slider-images-product owl-carousel owl-theme mt-3 p-3" data-responsive="[3,6,9]">
                    @foreach($product['gallery'] as $photo)
                        <img src="{{ $photo['src'] }}" data-image="{{ $photo['src'] }}"
                             class="slider-image-product" alt="{{ $photo['alt'] }}">
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
                               aria-label="Отзывы реальных покупателей">{{ $product['count_reviews'] }}</a>
                            @include('shop.nordihome.widgets.stars', ['rating' => $product['rating'], 'show_number' => true])
                        </div>
                    </div>
                    <div class="price-brand-block">
                        <div class="view-price">
                            @if($product['is_sale'])
                                @if(!$product['promotion']['has'])
                                    @if($product['price_previous'] > $product['price'])
                                        <div class="comment">* Цена на товар снижена</div>
                                        <span class="discount-price">{{ price($product['price']) }}</span>
                                        <span class="base-price">{{ price($product['price_previous']) }}</span>
                                    @else
                                        {{ price($product['price']) }}
                                    @endif
                                @else
                                    <div class="comment">* Цена по акции {{ $product['promotion']['title'] }}</div>
                                    <span class="discount-price">{{ price($product['promotion']['price']) }}</span>
                                    <span class="base-price">{{ price($product['price']) }}</span>
                                @endif
                            @else
                                {{ price($product['price']) }}
                            @endif
                            <div class="count-product">
                                @if($product['is_sale'])
                                    @if($product['quantity'] > 0)
                                        Товар в наличии
                                    @else
                                        Только под заказ
                                    @endif
                                @endif
                                <span class="" style="color: red; font-weight: bold;">Товар не продается! Цена вымышленная!</span>
                            </div>
                        </div>
                        <div class="view-brand">
                            @if(empty($product['brand']['src']))
                                <span>{{ $product['brand']['name'] }}</span>
                            @else
                                <img src="{{ $product['brand']['src'] }}"
                                     alt="{{ $product['brand']['name']  }}" title="{{ $product['brand']['name']  }}">
                            @endif

                        </div>
                    </div>
                    <div class="product-card-to-cart">
                        @if($product['is_sale'])
                            <button class="to-cart btn btn-dark e-add" data-product="{{ $product['id'] }}">В Корзину</button>


                            <button class="one-click btn btn-outline-dark"
                                    type="button" data-bs-toggle="modal"
                                    data-bs-target="#buy-click"
                                    onclick="document.getElementById('one-click-product-id').value={{$product['id']}};
                                    document.getElementById('button-buy-click').setAttribute('data-product', {{$product['id']}});"
                            >В 1 Клик!
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary" disabled>Снят с продажи</button>
                        @endif
                    </div>
                    @if(!is_null($product['modification']))
                        @include('shop.nordihome.product.__modification', ['modification' => $product['modification'], 'current_id' => $product['id']])
                    @endif
                    @if(!is_null($product['related']))
                        @include('shop.nordihome.product.__related', ['related' => $product['related']])
                    @endif

                    <div class="view-specifications">
                        @include('shop.nordihome.widgets.dimensions', ['dimensions' => $product['dimensions'], 'local' => $product['local'], 'region' => $product['delivery']])
                    </div>
                    <div style="color: #ff5555">САЙТ В РАЗРАБОТКЕ! Оригинальный сайт по адресу <a
                            href="https://nordihome.ru/" style="color: #5555ff">https://nordihome.ru/</a></div>
                </div>
            </div>
        </div>
        <div class="view-footer-product">
            @if(!empty($productAttributes))
                <div class="anchor-menu"><a href="#specifications">Характеристики</a></div>
            @endif
            <div class="anchor-menu"><a href="#description">Описание товара</a></div>
            <div class="product-code">Артикул <span>{{ $product['code'] }}</span></div>

        </div>
    </div>
    @if(!is_null($product['bonus']))
        @include('shop.nordihome.product._bonus', ['bonus' => $product['bonus']])
    @endif
    @if(!is_null($product['series']))
        @include('shop.nordihome.product._series', ['series' => $product['series']])
    @endif

    <div class="box-card">
        <h3 id="description">Описание</h3>
        {!! $product['description'] !!}
    </div>

    @include('shop.nordihome.product._attribute', ['productAttributes' => $productAttributes])
    @include('shop.nordihome.product._equivalent', ['equivalents' => $product['equivalents']])
    @include('shop.nordihome.product._reviews', ['reviews' => $product['reviews']])

    {!! $schema !!}
@endsection
