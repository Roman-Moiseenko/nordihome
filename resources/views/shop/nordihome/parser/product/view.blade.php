@extends('shop.nordihome.layouts.main')

@section('body', 'product')
@section('main', 'container-xl product-page parser')
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
                    <div class="price-brand-block">
                        <div class="view-price">

                                {{ price($product['price']) }}

                            <div class="count-product">
                                Только под заказ
                                <span class="" style="color: red; font-weight: bold;">Товар не продается! Цена вымышленная!</span>
                            </div>
                        </div>
                        <div class="view-brand">
                            <span>IKEA</span>
                        </div>
                    </div>
                    <div class="product-card-to-cart">
                        <button class="to-cart btn btn-dark e-add" data-product="{{ $product['id'] }}">В Корзину</button>


                        <button class="one-click btn btn-outline-dark"
                                type="button" data-bs-toggle="modal"
                                data-bs-target="#buy-click"
                                onclick="document.getElementById('one-click-product-id').value={{$product['id']}};
                                document.getElementById('button-buy-click').setAttribute('data-product', {{$product['id']}});"
                        >В 1 Клик!
                        </button>
                    </div>


                    <div class="view-specifications">
                        @include('shop.nordihome.widgets.dimensions', ['dimensions' => $product['dimensions'], 'local' => $product['local'], 'region' => $product['delivery']])
                    </div>
                    <div style="color: #ff5555">САЙТ В РАЗРАБОТКЕ! Оригинальный сайт по адресу <a
                            href="https://nordihome.ru/" style="color: #5555ff">https://nordihome.ru/</a></div>
                </div>
            </div>
        </div>
        <div class="view-footer-product">

            <div class="anchor-menu"><a href="#description">Описание товара</a></div>
            <div class="product-code">Артикул <span>{{ $product['code'] }}</span></div>

        </div>
    </div>


    <div class="box-card">
        <h3 id="description">Описание</h3>
        {!! $product['description'] !!}
    </div>

@endsection
