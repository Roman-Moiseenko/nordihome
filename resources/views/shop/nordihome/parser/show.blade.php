@extends('shop.nordihome.layouts.main')

@section('body', 'page calculate')
@section('main', 'container-xl')
@section('title', $title)
@section('description', $description)

@section('content')
    <h1>Заказ товаров с каталога IKEA.PL</h1>
    <div class="screen-action parser">
        <div class="left-list-block">
            <div class="parser-card-search box-card">
                <div class="header">
                    <p>Рассчитайте стоимость любого товара из каталога Икеа самостоятельно и Вы сразу узнаете стоимость заказа.</p>
                    <p><b>Для точного расчёта данный инструмент использовать без VPN</b></p>
                    <h3>Найти товар</h3>
                </div>
                <div class="find">
                    <div id="text" class="text">
                        Скопируйте и вставьте в поле номер артикула товара или ссылку с сайта <a href="https://IKEA.PL" target="_blank">IKEA.PL</a>
                    </div>
                    <form id="parser-search-form" method="post" action="{{ route('shop.parser.search') }}">
                        @csrf
                        <div class="form">
                            <input id="search-parser-field" type="text" name="search" class="form-control" required autocomplete="off"/>
                            <button id="search-parser-button" class="btn btn-dark py-2 px-4" type="submit">ИСКАТЬ</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="parser-list">
                @if(!empty($cart->items))
                <div class="parsing-title-products">
                    <div class="fs-3">Товары в корзине:</div>
                    <div class="button">
                        <a id="clear-button" class="btn btn-dark px-2"
                           onclick="event.preventDefault(); document.getElementById('form-clear-parser').submit();">Очистить корзину</a>
                        <form id="form-clear-parser" method="post" action="{{ route('shop.parser.clear') }}">
                            @csrf
                        </form>
                    </div>
                </div>
                @endif
                @foreach($cart->items as $i => $item)
                    @include('shop.layouts.parser._item', ['item' => $item])
                @endforeach
            </div>
        </div>
        <div class="right-action-block">
            <div id="parser-amount" class="sticky-block">
                <div class="">
                    <form id="to-order" method="POST" action="{{ route('shop.order.create-parser') }}">
                        @csrf
                        <button class="btn btn-dark w-100 py-3"  onclick="ym(88113821,'reachGoal','parser-prepare'); return true;"
                                @guest()
                                data-bs-toggle="modal" data-bs-target="#login-popup" type="button"
                                @endguest
                                @auth('user')
                                type="submit"
                                @endauth
                                @if(empty($cart->items))
                                disabled
                            @endif
                        >Перейти к оформлению
                        </button>
                    </form>
                    <div class="full-cart-order--info">
                        <div class="d-flex justify-content-between">
                            <div class="fs-5">Товаров в корзине</div>
                            <div id="cart-count-products" class="fs-5">{{ count($cart->items) }}</div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <div class="fs-6">Полная стоимость корзины</div>
                            <div id="amount" class="fs-6">{{ price($cart->amount) }}</div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="fs-7">Доставка до Калининграда (<span id="weight">{{$cart->weight}}</span> кг)</div>
                            <div id="delivery" class="fs-7">{{ price($cart->delivery) }}</div>
                        </div>
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <div class="fs-5">Сумма к оплате</div>
                            <div id="cart-amount-pay" class="fs-5">{{ price($cart->delivery + $cart->amount) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
