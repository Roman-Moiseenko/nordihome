<x-mail::message>
# Подтверждение регистрации

Укажите на сайте ваш код.
Ваш код активации {{ $verify_token }}

Или перейдите по ссылке:
@component('mail::button', ['url' => route('register.verify', ['token' => $verify_token])])
Подтвердить почту
@endcomponent

С уважением команда <br>
{{ config('app.name') }}
</x-mail::message>
