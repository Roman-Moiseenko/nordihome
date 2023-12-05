@extends('layouts.shop')

@section('body')
    product
@endsection

@section('main')
    container-xl
@endsection

@section('content')
    <div class="products-page">
        <div class="products-page-title d-flex h1">
            <h1>Моя корзина</h1>
        </div>
    </div>
    <div class="d-flex">
        <div class="full-cart-items">
            @foreach($cart as $item)
                <div class="full-cart-item">
                    <div class="full-cart-item--checked">
                        <input type="checkbox" checked>
                    </div>
                    <div class="full-cart-item--img">
                        <a href="{{ $item['url'] }}" target="_blank"><img src="{{ $item['img'] }}"/></a>
                    </div>
                    <div class="full-cart-item--info">
                        <a href="{{ $item['url'] }}" target="_blank"><span>{{ $item['name'] }}</span></a>
                    </div>
                    <div class="full-cart-item--change">
                        - <span>{{ $item['quantity'] }}</span> +
                    </div>
                </div>
            @endforeach
        </div>
        <div class="full-cart-order">
            <div>
            <button class="btn btn-dark w-100 py-3">Перейти к оформлению</button>
            <div class="full-cart-order--info">
                <div>Корзина    -----   x товаров, y кг</div>
                <div>Сумма заказа (базовая)</div>
                <div>Скидка</div>
            </div>
            </div>
        </div>
    </div>

@endsection


