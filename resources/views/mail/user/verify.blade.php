<x-mail::message>
# Подтверждение регистрации на сайте nordihome.ru

Ваш логин {{ $login }}

Укажите на сайте ваш код активации

# {{ $token }}


Или перейдите по ссылке:
@component('mail::button', ['url' => route('register.verify', ['token' => $token, 'agreement' => true])])
Подтвердить почту
@endcomponent

Я <a href="/page/soglasie-na-obrabotku-personalnyx-dannyx" target="_blank">согласен</a>
на обработку персональных данных. Подробнее об этом в <a href="/page/politika-obrabotki-personalnyx-dannyx" target="_blank">политике конфиденциальности

С уважением команда <br>
{{ config('app.name') }}
</x-mail::message>
