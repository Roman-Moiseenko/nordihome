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
        <div class="filters">
            <div class="borders">
                <div class="mobile-close"><i class="fa-light fa-xmark"></i></div>
                <div class="base-filter">
                    <x-widget.check name="in_stock" class="mt-2" checked="{{ $request->has('in_stock') }}">Только в наличии</x-widget.check>
                    <x-widget.numeric name="price" min-value="{{ $minPrice }}" max-value="{{ $maxPrice }}"
                                      current-min="{{ $request->has('price') ? $request->get('price')[0] : '' }}"
                                      current-max="{{ $request->has('price') ? $request->get('price')[1] : '' }}"
                                      class="mt-3">
                        Цена
                    </x-widget.numeric>
                    @if(!empty($brands))
                    <x-widget.variant class="mt-3" caption="Бренд">
                        @foreach($brands as $id => $brand)
                            <x-widget.variant-item name="brands[]" id="{{ $id }}" caption="{{ $brand['name'] }}"
                                                   image="{{ $brand['image'] }}"
                                                   checked="{{ $request->has('brands') ? in_array($id, $request->get('brands')) : false }}"
                                                   alt="{{ $brand['name'] }}"
                            />
                        @endforeach
                    </x-widget.variant>
                    @endif

                    <x-widget.check name="discount" class="mt-3" checked="{{ $request->has('discount') }}" >Акция</x-widget.check>
                </div>
                <div class="attribute-filter">
                    @foreach($prod_attributes as $attribute)
                        <div>
                            @if(isset($attribute['isBool']))
                                <x-widget.check name="a_{{ $attribute['id'] }}" class="mt-2" value="{{ $attribute['id'] }}"
                                                checked="{{ $request->has('a_' . $attribute['id']) }}">
                                    {{ $attribute['name'] }}</x-widget.check>
                            @endif
                            @if(isset($attribute['isNumeric']))
                                <x-widget.numeric name="a_{{ $attribute['id'] }}" min-value="{{ $attribute['min'] }}" max-value="{{ $attribute['max'] }}"
                                                  current-min="{{ $request->has('a_' . $attribute['id']) ? $request->get('a_' . $attribute['id'])[0] : '' }}"
                                                  current-max="{{ $request->has('a_' . $attribute['id']) ? $request->get('a_' . $attribute['id'])[1] : '' }}"
                                                  class="mt-3">
                                    {{ $attribute['name'] }}
                                </x-widget.numeric>

                            @endif
                            @if(isset($attribute['isVariant']))
                                <x-widget.variant class="mt-3" caption="{{ $attribute['name'] }}">
                                    @foreach($attribute['variants'] as $variant)
                                        <x-widget.variant-item name="a_{{ $attribute['id'] }}[]" id="{{ $variant['id'] }}"
                                                               caption="{{ $variant['name'] }}"
                                                               image="{{ $variant['image'] }}"
                                                               checked="{{ $request->has('a_' . $attribute['id']) ? in_array($variant['id'], $request->get('a_' . $attribute['id'])) : false }}"
                                                               alt="{{ $variant['name'] }}"
                                        />
                                    @endforeach
                                </x-widget.variant>
                            @endif
                            <hr/>
                        </div>
                    @endforeach
                </div>
                <div class="buttons-filter">
                <button class="btn btn-dark w-auto">Применить</button>
                <button id="clear-filter" class="btn btn-outline-dark w-auto" type="button">Сбросить</button>
            </div>
            </div>
        </div>
        <div class="list">
            @if(count($tags) > 0)
            <div class="box-card top-tags">
                @foreach($tags as $tag)
                    @if($tag_id == $tag->id)
                        <a href="{{ route('shop.category.view', [$category->slug]) }}"
                           class="tag-filter active" data-tag-id="{{ $tag->id }}">{{ $tag->name }}</a>
                    @else
                        <a href="{{ route('shop.category.view', [$category->slug, 'tag_id' => $tag->id]) }}"
                           class="tag-filter" data-tag-id="{{ $tag->id }}">{{ $tag->name }}</a>
                    @endif
                @endforeach
            </div>
            @endif
            <div class="products">
                <div class="row">
                @foreach($products as $product)
                    <div class="col-6 col-sm-4 col-lg-3 mb-5">
                        <livewire:shop.products.product-card :product="$product" :user="$user"/>
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
        let clearFilter = document.getElementById('clear-filter');
        clearFilter.addEventListener('click', function () {
            window.location.href = window.location.href.split("?")[0];
        });
    </script>

        {!! $schema->CategoryProductsPage($category) !!}

@endsection
