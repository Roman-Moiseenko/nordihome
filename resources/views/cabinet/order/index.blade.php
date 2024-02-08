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
                    <div class="fs-7">{{ $order->delivery->typeHTML() }} - {{ $order->delivery->address }}</div>
                    <div class="fs-7">Стоимость доставки - {{ ($order->delivery->cost == 0) ? 'Рассчитывается' : price($order->delivery->cost)}}</div>
                    <div class="fs-8">{{ $order->delivery->status->value() }}</div>
                </div>
                <div class="d-flex">
                    @foreach($order->items as $item)
                        <div>
                            <img src="{{ $item->product->photo->getThumbUrl('thumb') }}" style="width: auto; height: 80px;">
                            <span>{{ $item->quantity }} шт.</span>
                        </div>
                    @endforeach
                </div>

            </div>
            <div class="order-footer">
                {{ $order->statusHtml() }}
            </div>
        </div>
    @endforeach
@endsection
