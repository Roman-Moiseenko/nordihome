@php
    use App\Modules\Shop\Application\DTOs\CategoryIndexPageData;
    /** @var CategoryIndexPageData $pageData */
@endphp

@extends('shop.layouts.main')

@section('body', 'category')
@section('main', 'container-xl categories-page')
@section('title', $pageData->meta->title)
@section('description', $pageData->meta->description)


@section('content')
    <div class="title-page">
        <h1>Каталог товаров NORDIHOME</h1>
    </div>
    <div class="row">
        @foreach($pageData->categories as $category)
            @include('shop.catalog.card-category', ['category' => $category])
        @endforeach
    </div>


    <script type="application/ld+json" class="schemantra.com">

    </script>
@endsection
