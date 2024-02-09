@extends('cabinet.cabinet')
@section('body')
    @parent
    order
@endsection

@section('h1')
    Мои заказы
@endsection

@section('subcontent')
    @foreach($orders as $order)
        <div class="box-card">
            <div class="order-header" onclick="window.location.href='{{ route('cabinet.order.view', $order) }}'">
                <div>
                    <div class="fs-5">{{ $order->htmlDate() }}</div>
                    <div class="fs-8">{{ $order->htmlNum() }}</div>
                </div>
                <div>
                    <div class="fs-5">{{ price($order->total) }}</div>
                    <div class="fs-8">{{ $order->paid ? 'Оплачен' : '' }}</div>
                </div>
            </div>
            <div class="order-body">
                <div>
                    <div class="fs-7">{{ $order->delivery->typeHTML() }}<br>{{ $order->delivery->address }}</div>
                    <div class="fs-7 mt-1">Стоимость доставки
                        - {{ ($order->delivery->cost == 0) ? 'Рассчитывается' : price($order->delivery->cost)}}</div>
                    <div class="fs-8 mt-1">{{ $order->delivery->status->value() }}</div>
                </div>
                <div class="row position-relative">
                    @foreach($order->items()->paginate(4) as $item)
                        <div class="col-6 col-lg-3 ">
                            <div class="order-item-block">
                                <img src="{{ $item->product->photo->getThumbUrl('thumb') }}">
                                <span class="order-item-container"><span class="order-item-quantity fs-8">{{ $item->quantity }} шт.</span></span>
                            </div>
                        </div>
                    @endforeach
                    @if($order->items->count() > 4)
                        <span class="order-item-quantity--4" title="В заказе {{ $order->items->count() }} товаров">...</span>
                    @endif
                </div>

            </div>
            <div class="order-footer">
                {{ $order->statusHtml() }}
            </div>
        </div>
    @endforeach
@endsection
