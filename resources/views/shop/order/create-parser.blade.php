@extends('layouts.shop')

@section('body')
    product
@endsection

@section('main')
    container-xl order-page-create-parser
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
                            <input type="radio" name="payment" data-state="change" value="{{ $payment['class'] }}"
                                   data-sort="{{ $sort }}"
                                {{ ($payment['class'] == $default->payment->class_payment) ? 'checked' : '' }}>
                            <img src="{{ $payment['image'] }}" alt="{{ $payment['name'] }}" title="{{ $payment['name'] }}">
                        </label>
                    </div>
                @endforeach
                </div>
                <div id="invoice-data" {!! $default->payment->isInvoice() ? '' : ' style="display: none"' !!}>
                    <div {!! $default->payment->invoice() != '' ? '' : ' style="display: none"' !!}>
                        <span class="address-delivery--title"></span>
                        <span class="address-delivery--info"> {{ $default->payment->invoice() }} </span>
                        <span class="address-delivery--change" for="d---0">Изменить</span>
                        <input type="hidden" name="inn" id="input-inn-hidden">
                    </div>
                    <div class="input-group" id="d---0" {!! $default->payment->invoice() == '' ? '' : ' style="display: none"' !!}>
                        <input type="text" class="form-control" id="input-inn" aria-describedby="emailHelp" placeholder="Введите ИНН">
                        <button class="btn btn-outline-secondary input-to-hidden" type="button" from="input-inn" to="input-inn-hidden">Сохранить</button>
                    </div>

                </div>
            </div>
            <div class="box-card">
                <div>Доставка</div>
                <input type="radio" class="btn-check" name="delivery" data-state="change" id="delivery_storage" autocomplete="off"
                       value="{{ \App\Modules\Delivery\Entity\DeliveryOrder::STORAGE }}"
                       {{ $default->delivery->isStorage() ? 'checked' : '' }}
                >
                <label class="btn btn-outline-secondary" for="delivery_storage">Самовывоз</label>
                <input type="radio" class="btn-check" name="delivery" data-state="change" id="delivery_local" autocomplete="off"
                       value="{{ \App\Modules\Delivery\Entity\DeliveryOrder::LOCAL }}"
                    {{ $default->delivery->isLocal() ? 'checked' : '' }}
                >
                <label class="btn btn-outline-secondary" for="delivery_local">Доставка по региону</label>
                <input type="radio" class="btn-check" name="delivery" data-state="change" id="delivery_region" autocomplete="off"
                       value="{{ \App\Modules\Delivery\Entity\DeliveryOrder::REGION }}"
                    {{ $default->delivery->isRegion() ? 'checked' : '' }}
                >
                <label class="btn btn-outline-secondary" for="delivery_region">Транспортной компанией</label>

                <div class="block-delivery">
                    <div class="delivery-storage mt-3 p-3" {!! $default->delivery->isStorage() ? '' : ' style="display: none"' !!}>
                        @foreach($storages as $storage)
                            <div class="checkbox-group">
                                <input type="radio" class="form-check-inline" name="storage" data-state="change" id="{{ $storage->slug }}" autocomplete="off"
                                       value="{{ $storage->id }}"
                                    {{ $default->delivery->storage == $storage->id ? 'checked' : '' }}
                                >
                                <label for="{{ $storage->slug }}">{{ $storage->address }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="delivery-local mt-3 p-3" {!! $default->delivery->isLocal() ? '' : ' style="display: none"' !!}>
                        <div {!! $default->delivery->local->address != '' ? '' : ' style="display: none"' !!}>
                            <span class="address-delivery--title">Адрес доставки: </span>
                            <span class="address-delivery--info"> {{ $default->delivery->local->address }} </span>
                            <span class="address-delivery--change" for="d---1">Изменить</span>
                            <input type="hidden" name="address-local" id="input-delivery-local-hidden" value="{{ $default->delivery->local->address }}">
                            <input type="hidden" name="latitude-local" value="{{ $default->delivery->local->latitude }}">
                            <input type="hidden" name="longitude-local" value="{{ $default->delivery->local->longitude }}">
                            <input type="hidden" name="post-local" value="{{ $default->delivery->local->post }}">
                        </div>
                        <div class="input-group" id="d---1" {!! $default->delivery->local->address == '' ? '' : ' style="display: none"' !!}>
                            <input type="text" class="form-control" id="input-delivery-local" aria-describedby="emailHelp" placeholder="Начните вводить адрес">
                            <button class="btn btn-outline-secondary input-to-hidden" type="button" from="input-delivery-local" to="input-delivery-local-hidden">Сохранить</button>
                        </div>
                    </div>
                    <div class="delivery-region" {!! $default->delivery->isRegion() ? '' : ' style="display: none"' !!}>
                        <div id="slider-delivery-company" class="owl-carousel owl-theme mt-3 p-3">
                        @foreach($companies as $i => $company)
                            <label class="radio-img">
                                <input type="radio" name="company" data-state="change" value="{{ $company['class'] }}"
                                    {{ ($company['class'] == $default->delivery->company) ? 'checked' : '' }}>
                                <img src="{{ $company['image'] }}" alt="{{ $company['name'] }}" title="{{ $company['name'] }}">
                            </label>
                        @endforeach
                        </div>
                        <div {!! $default->delivery->region->address != '' ? '' : ' style="display: none"' !!}>
                            <span class="address-delivery--title">Адрес доставки: </span>
                            <span class="address-delivery--info"> {{ $default->delivery->region->address }} </span>
                            <span class="address-delivery--change" for="d---2">Изменить</span>
                            <input type="hidden" name="address-region" id="input-delivery-region-hidden" value="{{ $default->delivery->region->address }}">
                            <input type="hidden" name="latitude-region" value="{{ $default->delivery->region->latitude }}">
                            <input type="hidden" name="longitude-region" value="{{ $default->delivery->region->longitude }}">
                            <input type="hidden" name="post-region" value="{{ $default->delivery->region->post }}">
                        </div>
                        <div class="input-group" id="d---2" {!! $default->delivery->region->address == '' ? '' : ' style="display: none"' !!}>
                            <input type="text" class="form-control" id="input-delivery-region" aria-describedby="emailHelp" placeholder="Начните вводить адрес">
                            <button class="btn btn-outline-secondary input-to-hidden" type="button" from="input-delivery-region" to="input-delivery-region-hidden">Сохранить</button>
                        </div>
                    </div>

                </div>
            </div>
            <div class="box-card">
                <div>Список товаров в корзине</div>
                <div class="row">
                @foreach($cart->items as $item)

                    <div class="col-lg-2 col-sm-6 p-3">
                        <div class="" style="position: relative">
                            <img src="{{ $item['img'] }}" title="{{ $item['name'] }}" style="width: 100%;">
                            @if($item->quantity > 1)
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
                    <div class="d-flex justify-content-between">
                        <div class="fs-7">Стоимость доставки</div>
                        <div id="order-full-delivery" class="fs-7" >{{ price($delivery_cost->cost) }}</div>
                    </div>
                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <div class="fs-5">Сумма к оплате</div>
                        <div id="order-amount-pay" class="fs-5" data-base-cost="{{ $cart['common']['amount'] }}">{{ price($cart['common']['amount'] - $delivery_cost->cost) }}</div>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('shop.parser.view') }}" class="btn btn-outline-dark w-100 py-3">Вернуться к поиску</a>
                </div>
            </div>
        </div>
    </div>
@endsection
