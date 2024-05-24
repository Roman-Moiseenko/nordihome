<x-mail::message>
<x-mail::panel>
#Внесена предоплата по заказу {{ $order->htmlNumDate() }}
</x-mail::panel>

## Платеж в сумме {{ price($order->payment->amount) }} получен
Менеджер подготовит список товаров в соответствии с нераспределенной суммой по заказу

Список товаров возможных к выдаче:
@component('mail::table')
| Товар | Цена | Кол-во | Сумма |
| ----- |:----:|:------:| -----:|
@foreach($order->items as $item)
@if($item->getRemains() > 0)
| {{ $item->product->name}} | {{ price($item->sell_cost) }} | {{ $item->getRemains() }} | {{ price($item->sell_cost * $item->getRemains()) }} |
@endif
@endforeach
@endcomponent


С уважением,<br>
{{ config('app.name') }}
