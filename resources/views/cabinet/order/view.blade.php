@php
    use App\Modules\Cabinet\Application\DTOs\OrderClientPageData;
    /** @var OrderClientPageData[] $pageData */
$order = $pageData->order;
@endphp
@extends('cabinet.layout')
@section('body')
@parent
 order @endsection

@section('title', $pageData->meta->title)
@section('description', $pageData->meta->description)
@section('h1', $order->info->date . ' #00' . $order->info->number)

@section('subcontent')
    <div class="box-card p-2 mt-2">
        <div class="row">
            <div class="col-sm-6">
                <div>Сумма заказа: {{ price($order->info->baseAmount) }}</div>
                @if($order->info->baseAmount != $order->info->totalAmount)
                    <div>
                        Скидка на товары: {{ price($order->info->baseAmount - $order->info->totalAmount) }}
                    </div>
                @endif
                @if(!is_null($order->info->coupon))
                    <div>
                        Скидка на покупку (купон): {{ 'price($order->coupon)' }}
                    </div>
                @endif
                <div>Сумма к оплате: {{ price($order->info->totalAmount) }}</div>
                <div class="mt-3">
                <span class="badge bg-secondary">
                {{ $order->info->statusName }}
                </span>
                </div>
            </div>
            <div class="col-sm-6">
                @if($order->info->delivery > 0)
                    <div class="fs-7">{{ $order->info->address }}</div>
                    <div class="fs-7 mt-1">Стоимость доставки
                        - {{ price($order->info->delivery) }}</div>
                    <div class="fs-7 mt-1">

                    </div>
                @endif
            </div>
        </div>
    </div>

    @foreach($order->items as $item)
        <div class="box-card order-item">
            <div class="image">
                @if($item->productPublished)
                    <a href="{{ route('shop.product.view', $item->productSlug) }}" target="_blank">
                        <img src="{{ $item->productImage }}"/>
                    </a>
                @else
                    <img src="{{ $item->productImage }}"/>
                @endif
            </div>
            <div class="info">
                @if($item->productPublished)
                    <a href="{{ route('shop.product.view', $item->productSlug) }}" target="_blank">
                        {{ $item->productName }}
                    </a>
                @else
                    @if(!is_null($item->productParser))
                        <a href="{{ route('shop.ikea.product', $item->productCode) }}" target="_blank">
                            {{ $item->productName }}
                        </a>
                    @else
                        {{ $item->productName }}
                    @endif
                @endif
            </div>
            <div class="price">
                @if(is_null($item->discountId))
                    <div class="fs-6"> {{ $item->quantity }} шт х {{ price($item->baseCost) }}</div>
                    <div class="fs-5 fw-medium"
                         style="color: var(--bs-gray-900);"> {{ price($item->baseCost * $item->quantity) }}</div>
                @else
                    <div class="fs-7"> {{ price($item->baseCost) }} /шт.</div>
                    <div class="fs-7 red"> {{ $item->discountName() }}</div>
                    <div class="fs-6"> {{ $item->quantity }} шт х <span class="red">{{ price($item->sellCost) }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

@endsection
