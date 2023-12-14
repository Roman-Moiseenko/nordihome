@extends('layouts.shop')

@section('body')
    product
@endsection

@section('main')
    container-xl cart-page
@endsection

@section('content')
    <div class="title-page">
        <h1>@if(!empty($cart['items']))Моя корзина @else Ваша корзина пуста @endif</h1>
    </div>
    <div class="d-flex">
        <div class="left-list-block">
            <div class="box-card d-flex panel-cart-manage align-items-center">
                <div class="checkbox-group">
                    <input class="" type="checkbox" value="" id="checked-all" {{ $cart['common']['check_all'] ? 'checked' : '' }}>
                    <label class="" for="checked-all">Выбрать все</label>
                </div>
                <button id="cart-trash" class="btn btn-light ms-3 p-1" {!! $cart['common']['check_all'] ? '' : 'style="display:none;"' !!}>Удалить выбранные</button>
            </div>
            @foreach($cart['items'] as $item)
                <div class="box-card d-flex" id="full-cart-item-{{ $item['product_id'] }}">
                    <div class="full-cart-item--checked">
                        <input class="checked-item" type="checkbox" data-product="{{ $item['product_id'] }}"
                               {{ $item['check'] ? 'checked' : '' }} >
                    </div>
                    <div class="full-cart-item--img">
                        <a href="{{ $item['url'] }}" target="_blank"><img src="{{ $item['img'] }}"/></a>
                    </div>
                    <div class="full-cart-item--info">
                        <div>
                            <a href="{{ $item['url'] }}" target="_blank"><span>{{ $item['name'] }}</span></a>
                        </div>
                        <div class="full-cart-item--discount"
                             @if(is_null($item['discount_cost'])) style="display: none" @endif>
                            <span class="badge text-bg-danger">
                            {{ $item['discount_name'] }}
                            </span>
                        </div>
                        <div class="full-cart-item--costblock">
                            <div class="full-cart-item--cost"
                                 @if(!is_null($item['discount_cost'])) style="display: none" @endif>
                                <span class="current-cost">{{ price($item['cost']) }}</span>
                            </div>
                            <div class="full-cart-item--combinate"
                                 @if(is_null($item['discount_cost'])) style="display: none" @endif>
                                <span class="discount-cost">{{ price($item['discount_cost']) }}</span> <span
                                    class="current-cost">{{ price($item['cost']) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="full-cart-item-control">
                        <div class="set-value">
                            <button class="btn btn-outline-dark cartitem-sub" data-product="{{ $item['product_id'] }}">
                                <i class="fa-light fa-minus"></i></button>
                            <input type="text" class="form-control cartitem-set"
                                   data-product="{{ $item['product_id'] }}" value="{{ $item['quantity'] }}"/>
                            <button class="btn btn-outline-dark cartitem-plus" data-product="{{ $item['product_id'] }}">
                                <i class="fa-light fa-plus"></i></button>
                        </div>
                        <div class="text-center">
                            <span class="current-price">{{ price($item['price']) }}/шт.</span>
                        </div>
                        <div class="buttons">
                            <button class="btn btn-light cartitem-wish product-wish-toogle"
                                    data-product="{{ $item['product_id'] }}"><i
                                    class="fa-light fa-heart"></i></button>
                            <button class="btn btn-light cartitem-trash" data-product="{{ $item['product_id'] }}"><i
                                    class="fa-light fa-trash-can"></i></button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if(!empty($cart['items']))
            <div class="right-action-block">
                <div class="sticky-block">
                    <div>
                        <form id="to-order" method="POST" action="{{ route('shop.order.create') }}">
                            @csrf
                            <input type="hidden" name="preorder">
                        <button id="button-to-order" class="btn btn-dark w-100 py-3"
                                @guest()
                                data-bs-toggle="modal" data-bs-target="#login-popup"
                                @endguest
                                @auth('user')
                                type="submit"
                            @endauth
                        >Перейти к оформлению
                        </button>
                        </form>
                        <div class="full-cart-order--info">
                            <div class="d-flex justify-content-between">
                                <div class="fs-5">Товаров в корзине</div>
                                <div id="cart-count-products" class="fs-5">{{ $cart['common']['count'] }}</div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <div class="fs-6">Полная стоимость корзины</div>
                                <div id="cart-full-amount" class="fs-6">{{ price($cart['common']['full_cost']) }}</div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div class="fs-7">Ваша скидка</div>
                                <div id="cart-full-discount" class="fs-7">{{ price($cart['common']['discount']) }}</div>
                            </div>
                            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                <div class="fs-5">Сумма к оплате</div>
                                <div id="cart-amount-pay" class="fs-5">{{ price($cart['common']['amount']) }}</div>
                            </div>
                        </div>
                    </div>

                    <div id="cart-preorder" class="mt-3" {!! ($cart['common']['preorder']) ? '' : 'style="display: none"' !!}>
                        <div class="fs-6">В корзине имеется товар, которого нет в наличии.</div>
                        <div class="fs-7 mt-1">Вы можете выбрать убрать товар которого нет на складе, и заказать только по наличию.<br>
                            Либо, сделать заказ на выбранный объем, и дождаться пополнения склада, после чего получить весь товар одной доставкой.
                        </div>
                        <div class="checkbox-group mt-2">
                            <input id="preorder-false" type="radio" class="form-check-inline" data-state="change" autocomplete="off" name="pre-order">
                            <label for="preorder-false">Отгрузить по наличию на складе</label>
                        </div>
                        <div class="checkbox-group">
                            <input id="preorder-true" type="radio" class="form-check-inline" data-state="change" autocomplete="off" name="pre-order">
                            <label for="preorder-true">Отгрузить при пополнении на складе</label>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection


