<x-mail::message>
# Introduction

The body of your message.
Ваш код активации {{ $very_code }}

<x-mail::button :url="$url">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
