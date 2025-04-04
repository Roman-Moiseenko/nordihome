@extends('shop.nbrussia.layouts.main')

@section('body', 'products')
@section('main', 'container-xl products-page')
@if(isset($category))
@section('title', empty($title) ? ((is_null($category->parent_id) ? '' : $category->parent->name) . ' ' . $category->name . ' купить по цене от ' . $minPrice .
    '₽ ☛ Низкие цены ☛ Большой выбор ☛ Бесплатная доставка по всей России ★★★ Интернет-магазин ★★★ Оригинал Нью Баланс ☛ Честный знак' .
    $web->title_city . ' ☎ ' . $web->title_contact . '. Страница ' . $page) : ($title . '. Страница ' . $page))
@else
    @section('title', ($title . '. Страница ' . $page))
@endif
@section('description', $description)
@section('content')

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
            @include('shop.nbrussia.product.filter')
            <div class="list">
                <div class="title-page">
                    <div class="products-page-title">
                        <div class="title">
                            <h1>{{ isset($category) ? $category->name : 'Каталог' }} </h1>
                            <span>&nbsp;({{ $count_in_category }})</span>
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

                @if(count($tags) > 0)
                <div class="box-card top-tags">
                    @foreach($tags as $tag)
                        @if($tag_id == $tag->id)
                            <a href="{{ isset($category) ? route('shop.category.view', [$category->slug]) : route('shop.category.index') }}"
                               class="tag-filter active" data-tag-id="{{ $tag->id }}">{{ $tag->name }}</a>
                        @else
                            <a href="{{ isset($category) ? route('shop.category.view', [$category->slug]) : route('shop.category.index') }}"
                               class="tag-filter" data-tag-id="{{ $tag->id }}">{{ $tag->name }}</a>
                        @endif
                    @endforeach
                </div>
                @endif
                @if(isset($category))
                    <div class="top-text-category mb-3">
                        <h2>{{ $category->top_title }}</h2>
                        <div>
                            {!! $category->top_description !!}
                        </div>
                    </div>
                @endif

                <div class="products">
                    <div class="row">
                    @foreach($products as $product)
                        <div class="col-6 col-sm-4 col-lg-4 mb-5">
                            @include('shop.nbrussia.product.product-card', ['product' => $product])
                        </div>
                    @endforeach
                    </div>
                </div>

                <div class="products-page-list--bottom">
                    {{ $products->withPath($url_page)->links('shop.nbrussia.widgets.paginator') }}
                </div>
                @if(isset($category))
                    <div class="bottom-text-category">
                        {!! $category->bottom_text !!}
                    </div>
                @endif

            </div>
    </div>
    </form>
    <div class="recommendation-block">

    </div>

    <script>
        let clearFilter = document.getElementById('clear-filter');
        clearFilter.addEventListener('click', function () {
            window.location.href = window.location.href.split("?")[0];
        });
    </script>

    {!! $schema !!}

@endsection
