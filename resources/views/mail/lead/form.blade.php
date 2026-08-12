<x-mail::message>

<x-mail::panel>
#Новая заявка с формы
</x-mail::panel>

Форма {{ $data['form'] }}

@component('mail::table')
| Поле       | Значение  |
|:----------- | ------:|
@foreach($data as $key => $value)
| {{ $key}}       | {{ $value }}   |
@endforeach
@endcomponent
</x-mail::message>
