<x-mail::message>
<x-mail::panel>
#Получен онлайн-платеж по заказу {{ $order->htmlNumDate() }}
</x-mail::panel>

##Платеж № {{ $payment->document }} в сумме {{ price($payment->amount) }} получен

Не выданные товары по заказу:
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
