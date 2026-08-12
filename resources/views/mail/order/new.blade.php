<x-mail::message>

<x-mail::panel>
#Новый заказ {{ $order->number }}
</x-mail::panel>

@component('mail::table')
| Товар       | Цена         | Цена со скидкой  | Кол-во  | Сумма  |
| ----------- |:------------:|:----------------:|:-------:| ------:|
@foreach($order->items as $item)
| {{ $item->product->name}}       | {{ price($item->baseCost) }}         | {{ price($item->sellCost) }}  | {{ $item->quantity }}  | {{ price($item->sellCost * $item->quantity) }}  |
@endforeach
@endcomponent

Общая сумма к оплате {{ $order->amount->total }}
Ожидайте подтверждение менеджера.
Счет на оплату будет выслан после подтверждения

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
