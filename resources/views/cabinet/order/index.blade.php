@php
    use App\Modules\Cabinet\Application\DTOs\OrderClientData;
    /** @var OrderClientData[] $orders */
@endphp
@extends('cabinet.layout')
@section('body')
 @parent order @endsection

@section('h1', 'Мои заказы')


@section('subcontent')
    @foreach($orders as $order)
        <div class="box-card">
            <div class="order-header" onclick="window.location.href='{{ route('cabinet.order.view', $order->id) }}'">
                <div>
                    <div class="fs-5">{{ $order->info->date }}</div>
                    <div class="fs-8">Заказ #{{ $order->info->number }}</div>
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
                    <div class="fs-8 mt-1">{{ '' }}</div>
                </div>
                <div class="row position-relative">
                    @for($i = 0; $i < min(4, count($order->items)); $i++)
                        <div class="col-6 col-lg-3 ">
                            <div class="order-item-block">
                                <img src="{{ $order->items[$i]->image }}" title="{{ $order->items[$i]->name }}">
                                <span class="order-item-container"><span class="order-item-quantity fs-8">{{ $order->items[$i]->quantity }} шт.</span></span>
                            </div>
                        </div>
                    @endfor
                    @if(count($order->items) > 4)
                        <span class="order-item-quantity--4"
                              title="В заказе {{ count($order->items) }} товаров"><a href="{{ route('cabinet.order.view', $order->id) }}">...</a></span>
                    @endif
                </div>
            </div>
            <div class="order-footer">
                <span class="badge bg-secondary">
                {{ $order->info->statusName }}
                </span>
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
