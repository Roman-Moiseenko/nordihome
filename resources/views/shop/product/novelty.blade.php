@php use App\Modules\Shop\Application\DTOs\CategoryPageData; @endphp
@php
    /** @var CategoryPageData $pageData */
@endphp
@extends('shop.layouts.main')

@section('body', 'products novelty')
@section('main', 'container-xl products-page')
@section('title', $pageData->meta->title)
@section('description', $pageData->meta->description)

@section('content')
    <div class="title-page">
        <div class="products-page-title">
            <div class="title h1">
                <h1>{{ $pageData->category->name }}</h1>
                <span>&nbsp;{{ count_product($pageData->category->totalProducts) }} </span>
            </div>
            <div class="order btn-group">
                <div class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    Сортировка
                </div>
                <div class="dropdown-menu">
                    <ul>
                        <li class="{{ $pageData->filters->sortOrder == 'price-down' ? 'active' : '' }}"
                            data-order="price-down">По убыванию цены
                        </li>
                        <li class="{{ $pageData->filters->sortOrder == 'price-up' ? 'active' : '' }}"
                            data-order="price-up">По возрастанию цены
                        </li>
                        <li class="{{ $pageData->filters->sortOrder == 'name' ? 'active' : '' }}" data-order="name">По
                            названию
                        </li>
                        <li class="{{ $pageData->filters->sortOrder == 'rating' ? 'active' : '' }}" data-order="rating">
                            По популярности
                        </li>
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
                        <li class="{{ $pageData->filters->sortOrder == 'price-down' ? 'active' : '' }}"
                            data-order="price-down">По убыванию цены
                        </li>
                        <li class="{{ $pageData->filters->sortOrder == 'price-up' ? 'active' : '' }}"
                            data-order="price-up">По возрастанию цены
                        </li>
                        <li class="{{ $pageData->filters->sortOrder == 'name' ? 'active' : '' }}" data-order="name">По
                            названию
                        </li>
                        <li class="{{ $pageData->filters->sortOrder == 'rating' ? 'active' : '' }}" data-order="rating">
                            По популярности
                        </li>
                    </ul>
                </div>
            </div>
            <div class="filter-open"><i class="fa-sharp fa-light fa-filter-list"></i> Фильтры</div>
        </div>
        <div class="products-page-content d-flex position-relative">
            <div class="list">
                <div class="products">
                    <div class="row">
                        @foreach($pageData->products as $product)
                            <div class="col-6 col-sm-4 col-lg-3 mb-5">
                                @include('shop.product.product-card', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="products-page-list--bottom">
                    @include('shop.widgets.paginator', ['paginator' => $pageData->paginator])
                </div>
            </div>
        </div>
    </form>
    <div class="recommendation-block">

    </div>

@endsection
