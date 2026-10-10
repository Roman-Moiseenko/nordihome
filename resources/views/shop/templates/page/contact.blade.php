<!--template:Страница контакты-->
@php
    use App\Modules\Shop\Application\DTOs\Pages\PageViewPageData;
    /** @var PageViewPageData $pageData */
@endphp
@extends('layouts.main')
@section('main', 'pages')

@section('content')
        <div class="container-xl"><h1 class="my-4">{{ $pageData->page->name }}</h1></div>
        <div class="mt-4">
            {!! $pageData->page->text !!}
        </div>

        <script type="application/ld+json" class="schemantra.com">
            {!! json_encode($pageData->schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
        </script>
@endsection
