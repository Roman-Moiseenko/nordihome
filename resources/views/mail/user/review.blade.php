<x-mail::message>
<x-mail::panel>
#Оставьте отзыв по товарам {{ $order->htmlNumDate() }} исполнен
</x-mail::panel>

Оставьте отзыв(ы) по товарам, которые вы впервые у нас приобрели

@component('mail::table')
| Товар | Артикул | Ссылка на отзыв |
| ----- |:-------:|:---------------:|
@foreach($products as $product)
| {{ $product['name']}} | {{ $product['code'] }} | <a href="{{ $product['link_review'] }}">{{ $product['link_review'] }}</a> |
@endforeach
@endcomponent
@if($bonus_review)
Если Вы напишите отзыв по каждому товару из списка,
вы получите за каждый отзыв {{ price($bonus_amount) }} в виде купона,
который можно будет использовать в следующей покупке
@endif


С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
