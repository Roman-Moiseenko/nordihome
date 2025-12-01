@extends('shop.nordihome.cabinet.cabinet')
@section('body')
    @parent
    order
@endsection

@section('h1', $order->htmlDate() . ' ' . $order->htmlNum())

@section('subcontent')
    <div class="box-card p-2 mt-2">
        <div class="row">
            <div class="col-sm-6">
                <div>Сумма заказа: {{ price($order->getBaseAmount()) }}</div>
                @if($order->getDiscountProducts() != 0)
                    <div>
                        Скидка на товары: {{ price($order->getDiscountProducts()) }}
                    </div>
                @endif
                @if(!is_null($order->coupon_id))
                    <div>
                        Скидка на покупку (купон): {{ price($order->coupon) }}
                    </div>
                @endif
                <div>Сумма к оплате: {{ price($order->getTotalAmount()) }}</div>
                <div class="mt-3">
                <span class="badge bg-secondary">
                {{ $order->statusHtml() }}
                </span>
                </div>
            </div>
            <div class="col-sm-6">
                @if(!is_null($order->delivery))
                <div class="fs-7">{{ $order->delivery->typeHTML() }}<br>{{ $order->delivery->address }}</div>
                <div class="fs-7 mt-1">Стоимость доставки
                    - {{ ($order->delivery->cost == 0) ? 'Рассчитывается' : price($order->delivery->cost)}}</div>
                <div class="fs-7 mt-1">

                </div>
                @endif
            </div>
        </div>
    </div>

    @foreach($order->items as $item)
        <div class="box-card order-item">
            <div class="image">
                @if($item->product->isPublished())
                    <a href="{{ route('shop.product.view', $item->product->slug) }}" target="_blank">
                        <img src="{{ $item->product->getImage('mini') }}"/>
                    </a>
                @else
                    <img src="{{ $item->product->getImage('mini') }}"/>
                @endif
            </div>
            <div class="info">
                @if($item->product->isPublished())
                    <a href="{{ route('shop.product.view', $item->product->slug) }}" target="_blank">
                        {{ $item->product->name }}
                    </a>
                @else
                    {{ $item->product->name }}
                @endif
            </div>
            <div class="price">
                @if(is_null($item->discount_id))
                    <div class="fs-6"> {{ $item->quantity }} шт х {{ price($item->base_cost) }}</div>
                    <div class="fs-5 fw-medium"
                         style="color: var(--bs-gray-900);"> {{ price($item->base_cost * $item->quantity) }}</div>
                @else
                    <div class="fs-7"> {{ price($item->base_cost) }} /шт.</div>
                    <div class="fs-7 red"> {{ $item->discountName() }}</div>
                    <div class="fs-6"> {{ $item->quantity }} шт х <span class="red">{{ price($item->sell_cost) }}</span></div>
                @endif
            </div>
        </div>
    @endforeach

@endsection
