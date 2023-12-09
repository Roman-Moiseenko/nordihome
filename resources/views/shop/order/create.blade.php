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
            <div class="box-card">
                <div>Способы оплаты</div>
                <div id="slider-payment" class="owl-carousel owl-theme">
                @foreach($payments as $sort => $payment)
                    <div class="card-payment">
                        <label class="radio-img">
                            <input type="radio" name="payment" value="{{ $payment['class'] }}" data-sort="{{ $sort }}" {{ ($payment['class'] == $default['payment']['class']) ? 'checked' : '' }}>
                            <img src="{{ $payment['image'] }}" alt="{{ $payment['name'] }}" title="{{ $payment['name'] }}">
                        </label>
                    </div>
                @endforeach
                </div>
                <div id="invoice-data"></div>
            </div>
            <div class="box-card">
                <div>Доставка</div>
                <input type="radio" class="btn-check" name="delivery" id="delivery_storage" autocomplete="off">
                <label class="btn btn-outline-secondary" for="delivery_storage">Самовывоз</label>
                <input type="radio" class="btn-check" name="delivery" id="delivery_local" autocomplete="off">
                <label class="btn btn-outline-secondary" for="delivery_local">Доставка по региону</label>
                <input type="radio" class="btn-check" name="delivery" id="delivery_region" autocomplete="off">
                <label class="btn btn-outline-secondary" for="delivery_region">Транспортной компанией</label>

                <div class="block-delivery">
                    <div class="delivery-storage mt-3 p-3" style="display: none">
                        @foreach($storages as $storage)
                            <div class="checkbox-group">
                                <input type="radio" class="form-check-inline" name="storage" id="{{ $storage->slug }}" autocomplete="off" value="{{ $storage->id }}">
                                <label for="{{ $storage->slug }}">{{ $storage->address }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="delivery-local mt-3 p-3" style="display: none">
                        <div style="display: none; ">По умолчанию - кнопка изменить</div>
                        <div>
                            <label for="input-delivery-local" class="form-label">Адрес доставки</label>
                            <input type="text" class="form-control" id="input-delivery-local" aria-describedby="emailHelp" placeholder="Начните вводить адрес">
                        </div>
                    </div>
                    <div class="delivery-region" style="display: none">
                        <div id="slider-delivery-company" class="owl-carousel owl-theme mt-3 p-3">
                        @foreach($companies as $i => $company)
                            <label class="radio-img">
                                <input type="radio" name="company" value="{{ $company['class'] }}" {{ ($company['class'] == $default['delivery']['company']) ? 'checked' : '' }}>
                                <img src="{{ $company['image'] }}" alt="{{ $company['name'] }}" title="{{ $company['name'] }}">
                            </label>
                        @endforeach
                        </div>
                        <div style="display: none; ">По умолчанию - кнопка изменить</div>
                        <div>
                            <label for="input-delivery-region" class="form-label">Адрес доставки</label>
                            <input type="text" class="form-control" id="input-delivery-region" aria-describedby="emailHelp" placeholder="Начните вводить адрес">
                        </div>
                    </div>

                </div>
            </div>
            <div class="box-card">
                <div>Список товаров в корзине</div>
                <div class="row">
                @foreach($cart['items'] as $item)
                    <div class="col-lg-2 col-sm-6 p-3">
                        <div class="" style="position: relative">
                            <img src="{{ $item['img'] }}" title="{{ $item['name'] }}" style="width: 100%;">
                            @if($item['quantity'] > 1)
                                <span class="fs-8 order-item-quantity" style="position: absolute; bottom: 0">{{ $item['quantity'] }}шт.</span>
                            @endif
                        </div>
                        <div class="fs-7 text-center" style="color: var(--bs-gray-600);">{{ price($item['price']) }}/шт.</div>
                    </div>
                @endforeach
                </div>
            </div>
        </div>
        <div class="right-action-block">
            <div>
                <button id="button-to-order" class="btn btn-dark w-100 py-3">Оформить</button>
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
                <div class="d-flex justify-content-between">
                    <div class="fs-7">Стоимость доставки</div>
                    <div id="order-full-delivery" class="fs-7" >{{ '??' }}</div>
                </div>
                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <div class="fs-5">Сумма к оплате</div>
                    <div id="order-amount-pay" class="fs-5">{{ price($cart['common']['amount']) }}</div>
                </div>
            </div>
            <div class="mt-3">
                <div class="fs-5">Купон на скидку</div>
                <input type="text" class="form-control p-2" name="coupon" />
            </div>
        </div>
    </div>
@endsection
