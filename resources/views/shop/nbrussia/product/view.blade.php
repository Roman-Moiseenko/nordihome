@extends('shop.nbrussia.layouts.main')

@section('body', 'product')
@section('main', 'container-xl product-page')
@section('title', $title)
@section('description', $description)

@section('content')

    <div class="product-view">
        <div class="pre-block-gallery">
            <div class="block-gallery">
                <div>
                    <div class="gallery hide-mobile">
                        @foreach($product['gallery'] as $image)
                            <span class="item">
                    <img src="{{ $image['src'] }}" alt="{{ $image['alt'] }}" decoding="async"/>
                </span>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
        <div class="specification">
            <h1>{{ $product['name'] }}</h1>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
            *<br>
        </div>
    </div>

    <div class="product-video">

    </div>

    <div class="product-info-block">

    </div>

    <div class="recently">

    </div>
    <div class="product-review">

    </div>

    {!! ''; //$schema->ProductPage($product) !!}
@endsection
