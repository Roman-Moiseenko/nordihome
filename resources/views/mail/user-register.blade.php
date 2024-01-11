<x-mail::message>
# Introduction
<x-mail:panel>
    Вы зарегистрировались на нашем сайте
</x-mail:panel>
    Вам персональный купон на {{ $coupon->bonus }} рублей.
    Действителен до {{ $coupon->finished_at->translatedFormat('j F H:s') }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
