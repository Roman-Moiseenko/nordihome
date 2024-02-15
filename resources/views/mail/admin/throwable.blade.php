<x-mail::message>
# Ошибка на сайте:

{{ $throwble->getMessage() }}
{{ $throwble->getFile() }}
{{ $throwble->getLine() }}


@foreach($throwble->getTrace() as $i => $item)
{{ $i . ': ' . json_encode($item) }}
@endforeach


Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
