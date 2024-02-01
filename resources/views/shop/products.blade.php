@extends('layouts.shop')

@section('body')
    products
@endsection

@section('main')
    container-xl products-page
@endsection

@section('content')
    <div class="title-page">
        <div class="products-page-title d-flex h1">
            <h1>{{ $category->name }} </h1>
            <span>&nbsp;{{ \App\Modules\Shop\Helper::_countProd(count($products)) }} </span>
        </div>
    </div>
    <form action="" method="GET">
    <div class="products-page-content d-flex position-relative">
        <div class="products-page-filter">
            <div class="products-base-filter">
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
                                               checked="{{ $request->has('brands') ? in_array($id, $request->get('brands')) : false }}" />
                    @endforeach
                </x-widget.variant>
                @endif

                <x-widget.check name="discount" class="mt-3" checked="{{ $request->has('discount') }}" >Акция</x-widget.check>
            </div>

            <div class="products-attribute-filter">
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
                                    />
                                @endforeach
                            </x-widget.variant>
                        @endif
                        <hr/>
                    </div>
                @endforeach
            </div>
            <div class="products-buttons-filter">
                <button class="btn btn-dark w-auto">Применить</button>
                <button id="clear-filter" class="btn btn-outline-dark w-auto" type="button">Сбросить</button>
            </div>
        </div>
        <div class="products-page-list ms-3">
            <div class="products-page-list--top">
                @foreach($tags as $tag)
                    <span data-tag-id="{{ $tag->id }}">{{ $tag->name }}</span>
                @endforeach
            </div>
            <div class="products--list">
                <div class="row">
                @foreach($products as $product)
                    @include('shop.cards.card-3x')
                @endforeach
                </div>
            </div>

            <div class="products-page-list--bottom">
                Пагинация
            </div>

        </div>
    </div>
    </form>
    <div class="recommendation-block">
        Дополнительные блоки, слайдеры, вы смотрели и т.п.
    </div>

    <script>
        let clearFilter = document.getElementById('clear-filter');
        clearFilter.addEventListener('click', function () {
            window.location.href = window.location.href.split("?")[0];
        });
    </script>

@endsection
