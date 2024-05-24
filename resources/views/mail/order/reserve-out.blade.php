<x-mail::message>
<x-mail::panel>
#{{ $timeOut ? 'Закончился' : 'Заканчивается' }} резерв по заказу {{ $order->htmlNumDate() }}
</x-mail::panel>

@if($timeOut)
Заказ был отменен по причине окончания срока резерва
@else
До отмены заказа по истечению срока резерва осталось 12 часов. Оплатите заказ или внесите предоплату.
@endif

Товары в заказе:
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
