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
    <div class="products-page-content">
        <div class="products-page-filter">
            <div class="products-base-filter">
                <x-widget.check name="in_stock" class="mt-2">В наличии</x-widget.check>
                <x-widget.numeric name="price" min-value="{{ $minPrice }}" max-value="{{ $maxPrice }}" class="mt-3">
                    Цена
                </x-widget.numeric>
                @if(!empty($brands))
                <x-widget.variant class="mt-3" name="brands">
                    @foreach($brands as $id => $brand)
                        <x-widget.variant-item name="brands[]" id="{{ $id }}" caption="{{ $brand['name'] }}" img="{{ $brand['image'] }}"/>
                    @endforeach
                </x-widget.variant>
                @endif
                <x-widget.check name="discount" class="mt-3">Акция</x-widget.check>
            </div>

            <div class="products-attribute-filter">
                @foreach($prod_attributes as $attribute)
                    <div>
                        {{ $attribute->name }}
                        @if($attribute->isBool())
                            Виджет выбора чек
                        @endif
                        @if($attribute->isNumeric())
                            Виджет диапазона
                        @endif
                        @if($attribute->isVariant())
                            Виджет множественного выбора
                        @endif
                        <hr/>
                    </div>
                @endforeach

            </div>
            <div class="products-buttons-filter">
                <button>Применить</button>
                <button>Сбросить</button>
            </div>
        </div>
        <div class="products-page-list">
            <div class="products-page-list--top">
                @foreach($tags as $tag)
                    <span>{{ $tag->name }}</span>
                @endforeach
            </div>
            <div class="products--list">
                @foreach($products as $product)
                    <div> Карточка товара {{ $product->name }} </div>
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
