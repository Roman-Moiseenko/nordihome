<x-mail::message>

<x-mail::panel>
#Новый заказ с сайта
</x-mail::panel>
@if(!is_null($orderId()))
<a href="{{ route('admin.order.edit', $orderId) }}">Заказ</a>
@endif
@component('mail::table')
| Поле       | Значение  |
|:----------- | ------:|
@foreach($data as $key => $value)
    | {{ $key}}       | {{ $value }}   |
@endforeach
@endcomponent


</x-mail::message>
