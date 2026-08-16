@php
    /** @var \App\Modules\Cart\Application\DTOs\CartInfoData $cartInfo */
    $amountCommon = $cartInfo->amountCheck + $cartInfo->delivery + $cartInfo->deliveryParser - $cartInfo->discountCheck;
@endphp
@extends('layouts.main')

@section('body', 'order')
@section('main', 'container-xl order-page-create')
@section('title', 'Оформление товаров на покупку в NORDI HOME')

@section('content')
    <div class="title-page">
        <h1>Оформление заказа</h1>
    </div>
    <div class="screen-action">
        <div class="left-list-block">
            <!-- @ include('shop.order.widget.payment') -->
            @include('shop.order.widget.client')

            <div class="box-card">
                <div>Список товаров в корзине</div>
                <div class="row">
                    @foreach($cartInfo->items as $item)
                        @if($item->check)
                            @include('shop.order.widget.item', ['item' => $item])
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="right-action-block">
            <div class="sticky-block">
                <div>
                    <button id="button-to-order" class="btn btn-dark w-100 py-3"
                            onclick="document.getElementById('form-order-create').submit();">Оформить
                    </button>
                    <div class="d-flex justify-content-between mt-3">
                        <div class="fs-5">Ваш заказ</div>
                        <div id="order-count-products" class="fs-5">{{ $cartInfo->quantityCheck }} товар(а/ов)</div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <div class="fs-6">Полная стоимость</div>
                        <div id="order-full-amount" class="fs-6">{{ price($amountCommon) }}</div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="fs-7">Ваша скидка</div>
                        <div id="order-full-discount" class="fs-7">{{ price($cartInfo->discountCheck) }}</div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <div class="fs-7">Стоимость доставки *</div>
                        <div id="order-full-delivery"
                             class="fs-7">{{ price($cartInfo->delivery + $cartInfo->deliveryParser) }}</div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="fs-8">* рассчитывается отдельно, после оформления заказа</div>
                    </div>
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <div class="fs-5">Сумма по заказу</div>
                        <div id="order-amount-pay" class="fs-5"
                             data-base-cost="{{ $amountCommon }}">{{ price($amountCommon) }}</div>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="fs-5"></div>
                    <form id="form-order-create" method="POST" action="{{ route('shop.order.create') }}">
                        @method('PUT')
                        @csrf
                        <textarea class="form-control p-2" name="commentClient"
                                  placeholder="Комментарий к заказу"></textarea>
                        <input type="text" class="form-control mt-2 p-2" name="coupon" autocomplete="off"
                               placeholder="Купон на скидку"/>
                    </form>
                    <div class="coupon-info" style="display:none;">
                        <div>Скидка по купону:</div>
                        <div class="coupon-amount"></div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('shop.cart.view') }}" class="btn btn-outline-dark w-100 py-3">Вернуться в
                        корзину</a>
                </div>
            </div>
        </div>
    </div>
@endsection
