<x-mail::message>
    # Introduction

    Вы зарегистрировались на нашем сайте

    Вам персональный купон на {{ $coupon->bonus }} рублей.
    Действителен до {{ $coupon->finished_at->translatedFormat('j F H:s') }}
    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
