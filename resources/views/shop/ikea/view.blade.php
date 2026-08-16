@php
    use App\Modules\Shop\Application\DTOs\Pages\IkeaViewPageData;
    /** @var IkeaViewPageData $pageData */
@endphp
@extends('layouts.main')

@section('body', 'ikea')
@section('main', 'container-xl ikea-view')
@section('title', $pageData->meta->title)
@section('description', $pageData->meta->description)


@section('content')

    <h1>{{ $pageData->category->name }}. Товаров {{ $pageData->paginator->total }}</h1>

    <div class="row">
        <div class="col-lg-3">
            @include('shop.ikea.card-categories', [
                'categories' => $pageData->categories,
                'currentId' => $pageData->category->id
                ])
        </div>
        <div class="col-lg-9">
            <div class="products">
                <div class="row">
                    @foreach($pageData->products as $product)
                        <div class="col-6 col-sm-4 col-lg-3 mb-5">
                            @include('shop.ikea.card-product', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="products-page-list--bottom">
                @include('shop.widgets.paginator', ['paginator' => $pageData->paginator])
            </div>
        </div>
    </div>

    <!--script type="application/ld+json" class="schemantra.com">
        {!! ''; //json_encode($pageData->schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script-->
@endsection
