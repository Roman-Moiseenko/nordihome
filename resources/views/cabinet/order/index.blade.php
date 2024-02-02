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
                Самовывоз из ... адрес /Доставка по региону ... адрес/ДоставкаПочта РФ ... адрес<br>
                Статус
                заказа {{ \App\Modules\Order\Entity\Order\OrderStatus::STATUSES[$order->status->value] . ' ' . $order->status->comment}}
            </div>
        </div>
    @endforeach
@endsection
