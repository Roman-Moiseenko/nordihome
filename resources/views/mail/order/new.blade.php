<x-mail::message>

<x-mail::panel>
#Новый заказ {{ $order->htmlNum() }}
</x-mail::panel>

@component('mail::table')
| Товар       | Цена         | Цена со скидкой  | Кол-во  | Сумма  |
| ----------- |:------------:|:----------------:|:-------:| ------:|
@foreach($order->items as $item)
| {{ $item->product->name}}       | {{ price($item->base_cost) }}         | {{ price($item->sell_cost) }}  | {{ $item->quantity }}  | {{ price($item->sell_cost * $item->quantity) }}  |
@endforeach
@endcomponent

Общая сумма к оплате {{ $order->getTotalAmount() }}
Ожидайте подтверждение менеджера.
Счет на оплату будет выслан после подтверждения

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
