@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            {{ $modification->name }}
        </h2>
    </div>
    <div class="box p-5 mt-3">
        <div class="font-medium text-dark">
            Базовый товар: {{ $modification->base_product->name }} ({{ $modification->base_product->code }})
        </div>
        @foreach($modification->prod_attributes as $attribute)
            <div class="mt-3 flex items-center">
                <div class="image-fit w-10 h-10">
                    <img class="rounded-full" src="{{ $modification->base_product->getImage() }}"
                         alt="{{ $modification->name }}">
                </div>
                <div class="font-medium ml-3 text-lg text-primary">{{ $attribute->name }}</div>
            </div>
            <div class="flex ml-6 pl-10 flex-wrap">
                @foreach($attribute->variants as $variant)
                    <div class="ml-3 flex items-center">
                        <div class="image-fit w-6 h-6">
                            <img class="rounded-full" src="{{ $variant->getImage() }}"
                                 alt="{{ $variant->name }}">
                        </div>
                        <div class="ml-1 font-medium">{{ $variant->name }}</div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Список товаров
        </h2>
    </div>
    @foreach($modification->products as $product)
        <div> атрибуты и значение + кнопка удалить из модификации </div>
    @endforeach
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Добавить товары в модификацию
        </h2>
    </div>
    @foreach($modification->getVariations() as $i => $variation)
        <div>
            {{ $i + 1 }}
        @foreach($modification->prod_attributes as $attribute)
            {{ $attribute->name }} : {{ $attribute->getVariant($variation[$attribute->id])->name }} |
        @endforeach
        </div>
    @endforeach
@endsection
