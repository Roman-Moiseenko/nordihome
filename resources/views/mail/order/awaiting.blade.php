<x-mail::message>
<x-mail::panel>
#Ваш заказ {{ $order->htmlNumDate() }}
</x-mail::panel>

## Информация по заказу


@component('mail::table')
| Товар | Цена | Цена со скидкой | Кол-во | Сумма |
| ----- |:----:|:---------------:|:------:| -----:|
@foreach($order->items as $item)
| {{ $item->product->name}} | {{ price($item->base_cost) }} | {{ price($item->sell_cost) }} | {{ $item->quantity }} | {{ price($item->sell_cost * $item->quantity) }} |
@endforeach
@endcomponent


## Платежи по заказу
@component('mail::table')
| Платеж | Сумма | Способ оплаты | Ссылка на оплату |
| ----------- |:------------:|:----------------:|-------:|
@foreach($order->additions as $addition)
| {{ $addition->name }} | {{ price($payment->amount) }} | {{ $payment->nameType() }} | {{ $payment->document }} |
@endforeach
@endcomponent

Общая сумма к оплате {{ $order->getTotalAmount() }}


С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
