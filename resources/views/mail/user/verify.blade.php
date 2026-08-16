<x-mail::message>
# Подтверждение регистрации на сайте nordihome.ru

Ваш логин {{ $login }}

Укажите на сайте ваш код активации

# {{ $token }}


Или перейдите по ссылке:
@component('mail::button', ['url' => route('register.verify', ['token' => $token])])
Подтвердить почту
@endcomponent

С уважением команда <br>
{{ config('app.name') }}
</x-mail::message>
