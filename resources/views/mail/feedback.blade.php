<x-mail::message>
# Форма обратной связи

{{ $email }}
{{ $phone }}
{{ $message }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
