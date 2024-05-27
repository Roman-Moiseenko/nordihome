<x-mail::message>
<x-mail::panel>
#Уважаемый {{ $user->fullname->firstname }}
</x-mail::panel>

Вы получаете купон на скидку для покупки в <a href="{{ route('shop.category.index') }}">нашем магазине</a>
Скидка {{ price($coupon->bonus) }} действует до {{ $coupon->htmlFinish() }}
Начало действия купона {{ $coupon->htmlStart() }}

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
