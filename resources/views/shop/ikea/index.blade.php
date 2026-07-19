@php
    use App\Modules\Shop\Application\DTOs\Pages\IkeaIndexPageData;
    /** @var IkeaIndexPageData $pageData */
@endphp
@extends('shop.layouts.main')

@section('body', 'ikea')
@section('main', 'container-xl ikea-index')
@section('title', $pageData->meta->title)
@section('description', $pageData->meta->description)


@section('content')
    <div class="title-page">
        <h1>Каталог товаров ИКЕА</h1>
    </div>
    <div class="row">
        @foreach($pageData->categories as $category)
            @include('shop.ikea.card-category', ['category' => $category])
        @endforeach
    </div>


    <!--script type="application/ld+json" class="schemantra.com">
        {!! ''; //json_encode($pageData->schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script-->
@endsection
