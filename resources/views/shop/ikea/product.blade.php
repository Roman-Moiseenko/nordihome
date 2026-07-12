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

    <h1>{{ $pageData->product->name }}</h1>
    <!-- //TODO верстка левая панель -->
    <div>
        @include('shop.ikea.card-categories', [
            'categories' => $pageData->categories,
            'currentId' => $pageData->currentId
            ])
    </div>
    <!-- //TODO верстка правая панель -->
    <div>
        <!-- //TODO Товар -->
        <div class="product">
            {{ json_encode($pageData->product)  }}
        </div>
        <div>
            <button class="parser-to-cart">В корзину</button>
        </div>
    </div>
    <script type="application/ld+json" class="schemantra.com">
        {!! json_encode($pageData->schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection
