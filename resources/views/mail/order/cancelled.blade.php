<x-mail::message>

<x-mail::panel>
#Отмена заказа {{ $order->number }}
</x-mail::panel>

Ваш заказ был отменен менеджером:
@component('mail::table')
| Товар       | Цена         | Цена со скидкой  | Кол-во  | Сумма  |
| ----------- |:------------:|:----------------:|:-------:| ------:|
@foreach($order->items as $item)
| {{ $item->product->name}}       | {{ price($item->baseCost) }}         | {{ price($item->sellCost) }}  | {{ $item->quantity }}  | {{ price($item->sellCost * $item->quantity) }}  |
@endforeach
@endcomponent

<x-mail::panel>
##{{ $comment }}
</x-mail::panel>

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
