@php
    use App\Modules\Shop\Application\DTOs\Pages\PageViewPageData;
    /** @var PageViewPageData $pageData */
@endphp
@extends('layouts.main')
@section('main', 'container-xl pages')

@section('content')
    <div class="container-xl">
        <h1 class="my-4">{{ $pageData->page->name }}</h1>
        <div class="">
            Отзывы
        </div>
    </div>
    <script type="application/ld+json" class="schemantra.com">
        {!! json_encode($pageData->schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection
