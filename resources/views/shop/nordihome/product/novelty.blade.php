@extends('shop.nordihome.layouts.main')

@section('body', 'products novelty')
@section('main', 'container-xl products-page')
@section('title', $title)
@section('description', $description)

@section('content')
    <div class="title-page">
        <div class="products-page-title">
            <div class="title h1">
                <h1>Новинки</h1>
                <span>&nbsp;{{ count_product($count_in_category) }} </span>
            </div>
            <div class="order btn-group">
                <div class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Сортировка
                </div>
                <div class="dropdown-menu">
                    <ul>
                        <li class="{{ $order == 'price-down' ? 'active' : '' }}" data-order="price-down">По убыванию цены</li>
                        <li class="{{ $order == 'price-up' ? 'active' : '' }}" data-order="price-up">По возрастанию цены</li>
                        <li class="{{ $order == 'name' ? 'active' : '' }}" data-order="name">По названию</li>
                        <li class="{{ $order == 'rating' ? 'active' : '' }}" data-order="rating">По популярности</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <form action="" method="GET">
        <div class="mobile-manager">
            <div class="order btn-group">
                <div class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Сортировка
                </div>
                <div class="dropdown-menu">
                    <ul>
                        <li class="{{ $order == 'price-down' ? 'active' : '' }}" data-order="price-down">По убыванию цены</li>
                        <li class="{{ $order == 'price-up' ? 'active' : '' }}" data-order="price-up">По возрастанию цены</li>
                        <li class="{{ $order == 'name' ? 'active' : '' }}" data-order="name">По названию</li>
                        <li class="{{ $order == 'rating' ? 'active' : '' }}" data-order="rating">По популярности</li>
                    </ul>
                </div>
            </div>
            <div class="filter-open"><i class="fa-sharp fa-light fa-filter-list"></i> Фильтры</div>
        </div>
    <div class="products-page-content d-flex position-relative">
            <div class="products">
                <div class="row">
                @foreach($products as $product)
                    <div class="col-6 col-sm-4 col-lg-3 mb-5">
                        @include('shop.nordihome.product.product-card', ['product' => $product])
                    </div>
                @endforeach
                </div>
            </div>
            <div class="products-page-list--bottom">
                {{ $products->links('shop.nordihome.widgets.paginator') }}
            </div>
        </div>

    </form>
    <div class="recommendation-block">

    </div>

@endsection
