<x-mail::message>
@php
use App\Modules\Order\Application\DTOs\OrderViewData;
/** @var OrderViewData $data */
@endphp
<x-mail::panel>
#Новый заказ с сайта
</x-mail::panel>
@if(!is_null($orderId))
<a href="{{ route('admin.order.edit', $orderId) }}">Заказ #{{ $data->id }}</a>
@endif
@component('mail::table')
| Товар       | Артикул | Значение  |
|:----------- |:-------:| ------:|
@foreach($data->items as  $item)
| {{ $item->product->name }}       | {{ $item->product->code }}   | {{ $item->quantity }} |
@endforeach
@endcomponent

@if($data->isPickup)
## Самовывоз
@else
## Доставка {{ $data->address }}
@endif

##Клиент

Email:    {{ $data->client->email }}
Тел:    {{ $data->client->phone }}
Коментарий :    {{ $data->commentClient }}

</x-mail::message>
