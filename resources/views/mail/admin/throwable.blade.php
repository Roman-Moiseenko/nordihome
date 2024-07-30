<x-mail::message>
# Ошибка на сайте:

{{ $throwable->getMessage() }}
{{ $throwable->getFile() }}
{{ $throwable->getLine() }}


@foreach($throwable->getTrace() as $i => $item)
{{ $i . ':'}}
{{ 'file' . ($item['file'] ?? '-') . "\n" }}
{{ 'line: ' . ($item['line'] ?? '-') . "\n" }}
{{ 'function: ' . ($item['function'] ?? '') . "\n" }}
{{ 'class: ' . ($item['class'] ?? '') . "\n" }}
{{ 'type: ' . ($item['type'] ?? '') . "\n" }}
{{ 'args: ' . json_encode($item['args']) }}
<hr/>
@endforeach


Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
