<x-mail::message>

<x-mail::panel>
#Отмена заказа {{ $order->htmlNum() }}
</x-mail::panel>

Ваш заказ был отменен менеджером:
@component('mail::table')
| Товар       | Цена         | Цена со скидкой  | Кол-во  | Сумма  |
| ----------- |:------------:|:----------------:|:-------:| ------:|
@foreach($order->items as $item)
| {{ $item->product->name}}       | {{ price($item->base_cost) }}         | {{ price($item->sell_cost) }}  | {{ $item->quantity }}  | {{ price($item->sell_cost * $item->quantity) }}  |
@endforeach
@endcomponent

<x-mail::panel>
##{{ $comment }}
</x-mail::panel>

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
