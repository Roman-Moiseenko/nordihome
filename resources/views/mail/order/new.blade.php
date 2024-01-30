<x-mail::message>
# Introduction

    Новый заказ {{ $order->htmlNum() }}
    @component('mail::table')
        | Товар       | Цена         | Цена со скидкой  | Кол-во  | Сумма  |
        | ----------- |:------------:|:----------------:|:-------:| ------:|
    @foreach($order->items as $item)
        | {{ $item->product->name}}       | {{ price($item->base_cost) }}         | {{ price($item->sell_cost) }}  | {{ $item->quantity }}  | {{ price($item->sell_cost * $item->quantity) }}  |
    @endforeach
    @endcomponent
    Общая сумма к оплате {{ $order->total }}
    Ожидайте подтверждение менеджера.
    Счет на оплату будет выслан после подтверждения

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
