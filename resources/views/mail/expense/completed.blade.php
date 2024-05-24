<x-mail::message>
<x-mail::panel>
#Выдача товара по заказу {{ $order->htmlNumDate() }}
</x-mail::panel>

##Распоряжение на отгрузку {{ $expense->htmlNumDate() }} исполнено

@component('mail::table')
| Товар | Цена | Кол-во | Сумма |
| ----- |:----:|:------:| -----:|
@foreach($expense->items as $item)
| {{ $item->orderItem->product->name}} | {{ price($item->orderItem->sell_cost) }} | {{ $item->quantity }} | {{ price($item->orderItem->sell_cost * $item->quantity) }} |
@endforeach
@endcomponent

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
