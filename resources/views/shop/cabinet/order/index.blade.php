@php
    use App\Modules\Cabinet\Application\DTOs\OrderClientData;
    /** @var OrderClientData[] $orders */


@endphp
@extends('shop.cabinet.cabinet')
@section('body')
    @parent
    order
@endsection


@section('h1', 'Мои заказы')


@section('subcontent')
    @foreach($orders as $order)
        <div class="box-card">
            <div class="order-header" onclick="window.location.href='{{ route('cabinet.order.view', $order->id) }}'">
                <div>
                    <div class="fs-5">{{ $order->info->date }}</div>
                    <div class="fs-8">{{ $order->info->number }}</div>
                </div>
                <div>
                    <div class="fs-5">{{ price($order->info->totalAmount) }}</div>
                    <div class="fs-8">{{ $order->info->status == 'paid' ? 'Оплачен' : '' }}</div>
                </div>
            </div>
            <div class="order-body">
                <div>
                    <div class="fs-7">Доставка: <br>{{ $order->info->address }}</div>
                    <div class="fs-7 mt-1">Стоимость доставки*
                        - {{ price($order->info->delivery) }}</div>
                    <div class="fs-8 mt-1">{{ '$order->delivery->status->value()' }}</div>
                </div>
                <div class="row position-relative">
                    @foreach($order->items as $item)
                        <div class="col-6 col-lg-3 ">
                            <div class="order-item-block">
                                <img src="{{ $item->image }}" title="{{ $item->name }}">
                                <span class="order-item-container"><span class="order-item-quantity fs-8">{{ $item->quantity }} шт.</span></span>
                            </div>
                        </div>
                    @endforeach
                    @if(count($order->items) > 4)
                        <span class="order-item-quantity--4"
                              title="В заказе {{ count($order->items) }} товаров">...</span>
                    @endif
                </div>
                <div>
                    * список услуг
                </div>
            </div>
            <div class="order-footer">
                {{ $order->info->status }}
            </div>
        </div>
    @endforeach

    @if(count($orders) == 0 )
        <div class="fs-5 m-3 mb-5">
            У вас еще нет заказов.
        </div>
        <div class="fs-5 m-3 mb-5">Вы можете подобрать товар в нашем <a href="{{ route('shop.category.index') }}"
                                                                        class="btn btn-dark">Каталоге</a>
        </div>
    @endif
@endsection
