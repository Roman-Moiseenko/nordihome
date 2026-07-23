<x-mail::message>

<x-mail::panel>
#Новая заявка с форма {{ $data['form'] }}
</x-mail::panel>

@component('mail::table')
| Параметр       |   Значение   |
| -------------- |:------------:|
@foreach($data as $key => $value)
| {{ $key }}       | {{ $value }}         |
@endforeach
@endcomponent

{{ config('app.name') }}
</x-mail::message>
