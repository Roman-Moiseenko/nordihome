@extends('layouts.shop')

@section('body')
    product
@endsection

@section('main')
    container-xl order-page
@endsection

@section('content')
    <div class="title-page">
        <h1>Оформление заказа</h1>
    </div>
    <div class="d-flex">
        <div class="left-list-block">
            <div class="box-card">
                <div>Способы оплаты</div>
                <div id="slider-payment" class="owl-carousel owl-theme">
                @foreach($payments as $payment)
                    <div class="card-payment">
                        <img src="{{ $payment['image'] }}" alt="{{ $payment['name'] }}" title="{{ $payment['name'] }}" >
                    </div>
                @endforeach
                </div>
            </div>
            <div class="box-card">
                Доставка/самовывоз
            </div>
            <div class="box-card">
                <div>Список товаров в корзине</div>
                @foreach($cart['items'] as $item)
                    <div>
                        {{ $item['name'] }} - {{ $item['quantity'] }}
                    </div>
                @endforeach
            </div>
        </div>
        <div class="right-action-block">
            <div>
                Оплатить / Сохранить<br>
                Промокоды и купоны
            </div>
        </div>
    </div>
@endsection
