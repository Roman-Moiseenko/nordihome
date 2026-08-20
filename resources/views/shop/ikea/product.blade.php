@php
    use App\Modules\Shop\Application\DTOs\Pages\IkeaProductPageData;
    /** @var IkeaProductPageData $pageData */
    $product = $pageData->product;

    $galleryItems = [];
    foreach ($product->images as $img) {
        $galleryItems[] = [
            'src' => $img->full,
            'caption' => $img->alt ?? '',
        ];
    }

@endphp
@extends('layouts.main')

@section('body', 'ikea')
@section('main', 'container-xl ikea-product')
@section('title', $pageData->meta->title)
@section('description', $pageData->meta->description)


@section('content')
    <h1>{{ $pageData->product->name }}</h1>

    <div class="row">
        <div class="col-lg-3">
            @include('shop.ikea.card-categories', [
                'categories' => $pageData->categories,
                'currentId' => $pageData->currentId
                ])
        </div>

        <div class="col-lg-9">

            <div class="box-card">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="view-image-product">
                            @if(!empty($product->images))
                                <img id="main-image-product" src="{{ $product->images[0]->src }}" style="width: 100%; cursor: pointer" data-index="0">
                            @endif
                        </div>

                        <div class="slider-images-product owl-carousel owl-theme mt-3 p-3" data-responsive="[3,6,9]">
                            @foreach($product->images as $index => $photo)
                                <img src="{{ $photo->mini }}" data-image="{{ $photo->src }}"
                                     class="slider-image-product" alt="{{ $photo->alt }}" data-index="{{ $index }}">
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div>
                            <h1>{{ $product->name }} </h1>
                            <div class="product-code">
                                Артикул: {{ codeIkea($product->code) }}
                            </div>
                            <div>
                                {{ $product->short }}
                            </div>
                        </div>
                        <div>
                            @foreach($product->colors as $color) {{ $color }}, @endforeach
                        </div>
                        <div>
                            Цена {{ price($product->price, 'zł') }}
                        </div>
                        <div>
                            <button
                                class="to-cart btn btn-black e-add"
                                data-product="{{ $product->id }}"
                                data-parser="1"
                            >
                                В корзину
                            </button>
                        </div>

                        <h4>Упаковка</h4>
                        @foreach($product->packages as $package)
                            <h5>Пачка</h5>
                            <b>Длина:</b> {{ $package['length'] }} см<br>
                            <b>Ширина:</b> {{ $package['width'] }} см<br>
                            <b>Высота:</b> {{ $package['height'] }} см<br>
                            <b>Вес:</b> {{ $package['weight'] }} кг<br>
                            <b>Кол-во:</b> {{ $package['quantity'] }} шт<br>
                        @endforeach
                        @if(!empty($product->composite))
                        <p><b>Состав</b> </p>
                            {!! json_encode($product->composite)  !!}
                        @endif

                    </div>
                </div>
            </div>

            <div class="box-card">
                @if(!empty($product->variants))
                    <h4>Варианты</h4>
                <div>
                    @foreach($product->variants as $variant)
                        <a href="{{ route('shop.ikea.product', $variant->code) }}">
                            <img src="{{ $variant->image }}" alt="" title="{{ codeIkea($variant->code) }}">
                        </a>
                    @endforeach
                </div>
                @endif
                <h4>Габариты товара</h4>
                @foreach($product->dimensions as $key => $value)
                    <b>{{ $key }}: </b> {{ $value }}<br>
                @endforeach
                <h4>Описание</h4>
                <div>
                    {!! $product->description !!}
                </div>

                <h4>Материалы и уход</h4>
                <p><b>Материалы</b></p>
                @foreach($product->materials as $key => $value)
                    <p><b>{{ $key }}</b> {{ $value }}</p>
                @endforeach

                <p><b>Уход</b></p>
                {!! $product->care !!}
            </div>
        </div>
    </div>
    <script>
        window.productGalleryData = @json($galleryItems);
    </script>
    {{-- Подключаем наш отдельный JS-файл --}}
    @vite('resources/js/nordihome/product.js')
    <!--script type="application/ld+json" class="schemantra.com">
        {!! ''; //json_encode($pageData->schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script-->
@endsection
