@extends('layouts.shop')

@section('body')
    products
@endsection

@section('main')
    container-xl
@endsection

@section('content')
    <div class="products-page">
        <div class="products-page-title d-flex h1">
            <h1>{{ $category->name }} </h1>
            <span>&nbsp;{{ \App\Modules\Shop\Helper::_countProd(count($products)) }} </span>
        </div>
    </div>
    <div class="products-page-content d-flex position-relative">
        <div class="products-page-filter">
            <div class="products-base-filter">
                <x-widget.check name="in_stock" class="mt-2">В наличии</x-widget.check>
                <x-widget.numeric name="price" min-value="{{ $minPrice }}" max-value="{{ $maxPrice }}" class="mt-3">
                    Цена
                </x-widget.numeric>
                @if(!empty($brands))
                <x-widget.variant class="mt-3" caption="Бренд">
                    @foreach($brands as $id => $brand)
                        <x-widget.variant-item name="brands[]" id="{{ $id }}" caption="{{ $brand['name'] }}" image="{{ $brand['image'] }}"/>
                    @endforeach
                </x-widget.variant>
                @endif
                <x-widget.check name="discount" class="mt-3">Акция</x-widget.check>
            </div>

            <div class="products-attribute-filter">
                @foreach($prod_attributes as $attribute)
                    <div>
                        @if(isset($attribute['isBool']))
                            <x-widget.check name="attribute_{{ $attribute['id'] }}" class="mt-2" value="{{ $attribute['id'] }}">
                                {{ $attribute['name'] }}</x-widget.check>
                        @endif
                        @if(isset($attribute['isNumeric']))
                            <x-widget.numeric name="attribute_{{ $attribute['id'] }}" min-value="{{ $attribute['min'] }}" max-value="{{ $attribute['max'] }}" class="mt-3">
                                {{ $attribute['name'] }}
                            </x-widget.numeric>
                        @endif
                        @if(isset($attribute['isVariant']))
                            <x-widget.variant class="mt-3" caption="{{ $attribute['name'] }}">
                                @foreach($attribute['variants'] as $variant)
                                    <x-widget.variant-item name="attribute_{{ $attribute['id'] }}[]" id="{{ $variant['id'] }}"
                                                           caption="{{ $variant['name'] }}"
                                                           image="{{ $variant['image'] }}"/>
                                @endforeach
                            </x-widget.variant>
                        @endif
                        <hr/>
                    </div>
                @endforeach

            </div>
            <div class="products-buttons-filter">
                <button class="btn btn-dark w-auto">Применить</button>
                <button class="btn btn-outline-dark w-auto">Сбросить</button>
            </div>
        </div>
        <div class="products-page-list ms-3">
            <div class="products-page-list--top">
                @foreach($tags as $tag)
                    <span>{{ $tag->name }}</span>
                @endforeach
            </div>
            <div class="products--list">
                @foreach($products as $product)
                    @include('shop.cards.card-3x')

                @endforeach
            </div>

            <div class="products-page-list--bottom">
                Пагинация
            </div>

        </div>

    </div>
    <div class="recommendation-block">
        Дополнительные блоки, слайдеры, вы смотрели и т.п.
    </div>

@endsection
