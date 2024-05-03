@extends('layouts.shop')

@section('body', 'order')
@section('main', 'container-xl order-page-create-parser')
@section('title', 'Оформление товаров под заказ - NORDI HOME')

@section('content')
    <div class="title-page">
        <h1>Оформление заказа</h1>
    </div>
    <div class="screen-action">
        <div class="left-list-block">
            @include('shop.order.widget.payment')
            @include('shop.order.widget.delivery')
            @include('shop.order.widget.personal')
            <div class="box-card">
                <div>Список товаров в корзине</div>
                <div class="row">
                @foreach($cart->items as $item)
                        @include('shop.order.widget.item-parser', ['item' => $item])
                @endforeach
                </div>
            </div>
        </div>
        <div class="right-action-block">
            <div class="sticky-block">
                <div>
                    <button id="button-to-order" class="btn btn-dark w-100 py-3" onclick="document.getElementById('form-order-create-parser').submit();">{{ $user->payment->online() ? 'Оплатить' : 'Оформить' }} </button>
                    <div class="d-flex justify-content-between mt-3">
                        <div class="fs-5">Ваш заказ</div>
                        <div id="order-count-products" class="fs-5">{{ $cart->weight }} * масса в кг</div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <div class="fs-6">Стоимость товаров</div>
                        <div id="order-full-amount" class="fs-6">{{ price($cart->amount) }}</div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="fs-7">Стоимость доставки до Калининграда</div>
                        <div id="order-full-delivery-kld" class="fs-7" >{{ price($cart->delivery) }}</div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <div class="fs-7">Стоимость доставки*</div>
                        <div id="order-full-delivery" class="fs-7" >{{ price($delivery_cost->cost) }}</div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="fs-8">* рассчитывается отдельно, после оформления заказа</div>
                    </div>

                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <div class="fs-5">Сумма к оплате</div>
                        <div id="order-amount-pay" class="fs-5" data-base-cost="{{ $cart->amount + $cart->delivery }}">{{ price($cart->amount + $cart->delivery) }}</div>
                    </div>
                </div>
                <form id="form-order-create-parser" method="POST" action="{{ route('shop.order.create-parser') }}">
                    @method('PUT')
                    @csrf
                </form>
                <div class="mt-3">
                    <a href="{{ route('shop.parser.view') }}" class="btn btn-outline-dark w-100 py-3">Вернуться к поиску</a>
                </div>
            </div>
        </div>
    </div>
@endsection
