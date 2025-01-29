<x-mail::message>
<x-mail::panel>
#Товар по распоряжению {{ $expense->htmlNumDate() }} отправлен
</x-mail::panel>

##Посылке присвоен трек-номер <a href="{{ $expense->delivery->url . '/' . $expense->delivery->track_number }}">{{ $expense->delivery->track_number }}</a>

###Список товара:
@component('mail::table')
| Товар | Цена | Кол-во | Сумма |
| ----- |:----:|:------:| -----:|
@foreach($expense->items as $item)
| {{ $item->orderItem->product->name}} | {{ price($item->orderItem->sell_cost) }} | {{ $item->quantity }} | {{ price($item->orderItem->sell_cost * $item->quantity) }} |
@endforeach
@endcomponent

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
