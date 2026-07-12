@php
    use App\Modules\Shop\Application\DTOs\Pages\IkeaProductPageData;
    /** @var IkeaProductPageData $pageData */
@endphp
@extends('shop.layouts.main')

@section('body', 'ikea')
@section('main', 'container-xl ikea-product')
@section('title', $pageData->meta->title)
@section('description', $pageData->meta->description)


@section('content')

<h1>Карточка товара</h1>


    <script type="application/ld+json" class="schemantra.com">
        {!! json_encode($pageData->schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection
