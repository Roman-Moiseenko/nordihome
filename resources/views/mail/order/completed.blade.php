<x-mail::message>
<x-mail::panel>
#Ваш заказ {{ $order->htmlNumDate() }} исполнен
</x-mail::panel>

Заказ полностью исполнен. Отгрузки товара по заказу:

@foreach($order->expenses as $expense)
## Распоряжение {{ $expense->htmlNumDate() }}

@component('mail::table')
| Товар | Цена | Кол-во | Сумма |
| ----- |:----:|:------:| -----:|
@foreach($expense->items as $item)
| {{ $item->orderItem->product->name}} | {{ price($item->orderItem->sell_cost) }} | {{ $item->quantity }} | {{ price($item->orderItem->sell_cost * $item->quantity) }} |
@endforeach
@endcomponent

@endforeach


С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
