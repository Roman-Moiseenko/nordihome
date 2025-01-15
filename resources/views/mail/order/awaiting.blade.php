<x-mail::message>
<x-mail::panel>
#Ваш заказ {{ $order->htmlNumDate() }}
</x-mail::panel>

## Информация по заказу


@component('mail::table')
| Товар | Цена | Цена со скидкой | Кол-во | Сумма |
| ----- |:----:|:---------------:|:------:| -----:|
@foreach($order->items as $item)
| {{ $item->product->name}} | {{ price($item->base_cost) }} | {{ price($item->sell_cost) }} | {{ $item->quantity }} | {{ price($item->sell_cost * $item->quantity) }} |
@endforeach
@endcomponent


## Услуги по заказу
@component('mail::table')
| Услуга | Сумма | Комментарий |
| ----------- |:------------:|:----------------:|-------:|
@foreach($order->additions as $addition)
| {{ $addition->addition->name }} | {{ price($addition->amount) }} | {{ $addition->comment }} |
@endforeach
@endcomponent

Общая сумма к оплате {{ $order->getTotalAmount() }}


С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
