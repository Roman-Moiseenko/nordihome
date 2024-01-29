@extends('layouts.shop')

@section('body')
    product
@endsection

@section('main')
    container-xl order-page-create
@endsection

@section('content')
    <div class="title-page">
        <h1>Оформление заказа</h1>
    </div>
    <div class="d-flex">
        <div class="left-list-block">
            @include('shop.order.widget.payment')
            @include('shop.order.widget.delivery')

            <div class="box-card">
                <div>Список товаров в корзине</div>
                <div class="row">
                @foreach($cart['items_order'] as $item)
                    @if($item['check'])
                    <div class="col-lg-2 col-sm-6 p-3">
                        <div class="" style="position: relative">
                            <img src="{{ $item['img'] }}" title="{{ $item['name'] }}" style="width: 100%;">
                            @if($item['quantity'] > 1)
                                <span class="fs-8 order-item-quantity" style="position: absolute; bottom: 0">{{ $item['quantity'] }}шт.</span>
                            @endif
                        </div>
                        <div class="fs-7 text-center" style="color: var(--bs-gray-600);">{{ price($item['price']) }}/шт.</div>
                    </div>
                    @endif
                @endforeach
                </div>
                @if(!empty($cart['items_preorder']))
                    <div class="mt-3">Товары для предзаказа</div>
                    <div class="row">
                        @foreach($cart['items_preorder'] as $item)
                            @if($item['check'])
                                <div class="col-lg-2 col-sm-6 p-3">
                                    <div class="" style="position: relative">
                                        <img src="{{ $item['img'] }}" title="{{ $item['name'] }}" style="width: 100%;">
                                        @if($item['quantity'] > 1)
                                            <span class="fs-8 order-item-quantity" style="position: absolute; bottom: 0">{{ $item['quantity'] }}шт.</span>
                                        @endif
                                    </div>
                                    <div class="fs-7 text-center" style="color: var(--bs-gray-600);">{{ price($item['price']) }}/шт.</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="right-action-block">
            <div class="sticky-block">
                <div>
                    <button id="button-to-order" class="btn btn-dark w-100 py-3" onclick="document.getElementById('form-order-create').submit();">{{ $default->payment->online() ? 'Оплатить' : 'Оформить' }} </button>
                    <div class="d-flex justify-content-between mt-3">
                        <div class="fs-5">Ваш заказ</div>
                        <div id="order-count-products" class="fs-5">{{ $cart['common']['count'] }} * масса в кг</div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <div class="fs-6">Полная стоимость</div>
                        <div id="order-full-amount" class="fs-6">{{ price($cart['common']['full_cost']) }}</div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="fs-7">Ваша скидка</div>
                        <div id="order-full-discount" class="fs-7">{{ price($cart['common']['discount']) }}</div>
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
                        <div id="order-amount-pay" class="fs-5" data-base-cost="{{ $cart['common']['amount'] }}">{{ price($cart['common']['amount']) }}</div>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="fs-5">Купон на скидку</div>
                    <form id="form-order-create" method="POST" action="{{ route('shop.order.create') }}">
                        @method('PUT')
                        @csrf
                    <input type="text" class="form-control p-2" name="coupon" />

                        <input type="hidden" name="preorder" value="{{ $preorder }}">
                    </form>
                    <div class="coupon-info" style="display:none;">
                        <div>Скидка по купону:</div>
                        <div class="coupon-amount"></div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('shop.cart.view') }}" class="btn btn-outline-dark w-100 py-3">Вернуться в корзину</a>
                </div>
            </div>
        </div>
    </div>
@endsection
