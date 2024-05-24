<x-mail::message>
<x-mail::panel>
#Ваш заказ {{ $order->htmlNumDate() }} оплачен
</x-mail::panel>

## В ближайшее время менеджер приступит к отгрузке товара

Список товаров к выдаче:
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
</x-mail::message>

