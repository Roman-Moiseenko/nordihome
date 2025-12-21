@extends('shop.nordihome.layouts.main')

@section('body', 'products')
@section('main', 'container-xl products-page')
@section('title', $title)
@section('description', $description)

@section('content')
    <div class="title-page">
        <div class="products-page-title">
            <div class="title h1">
                <h1>{{ $category->name }} </h1>
                <span>&nbsp;{{ count_product(count($products)) }} </span>
            </div>
        </div>
    </div>
    <form action="" method="GET">
        <div class="mobile-manager">
        </div>
    <div class="products-page-content position-relative" style="display: flex;">
        @include('shop.nordihome.parser.product.filter')
        <div class="list">
            <div class="products">
                <div class="row">
                @foreach($products as $product)
                    <div class="col-6 col-sm-4 col-lg-3 mb-5">
                        @include('shop.nordihome.parser.product.card', ['product' => $product])

                    </div>
                @endforeach
                </div>
            </div>

            <div class="products-page-list--bottom">
                {{ $products->links('shop.nordihome.widgets.paginator') }}
            </div>

        </div>
    </div>
    </form>
    <div class="recommendation-block">

    </div>

    <script>
     /*   let clearFilter = document.getElementById('clear-filter');
        clearFilter.addEventListener('click', function () {
            window.location.href = window.location.href.split("?")[0];
        });*/
    </script>

@endsection
