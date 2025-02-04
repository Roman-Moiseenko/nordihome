<x-mail::message>
<x-mail::panel>
#Отзыв нужен
</x-mail::panel>

Вы приобрели следующие товары:
@component('mail::table')
| Товар | Ссылка на товар |
| ----- | --------------- |
@foreach($expense->items as $item)
| {{ $item->orderItem->product->name}} | <a href="{{ route('shop.product.view', $item->orderItem->product->slug) }}">Оставить отзыв</a> |
@endforeach
@endcomponent

Напишите по ним свой отзыв

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
