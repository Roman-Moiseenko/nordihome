@extends('shop.nordihome.layouts.main')

@section('body', 'category parser')
@section('main', 'container-xl catalogs-page')
@section('title', $title)
@section('description', $description)


@section('content')
    <div>
        <div class="parser-search" data-route="{{ route('shop.parser.search') }}">
            <div class="presearch-wrapper">
                <input id="parser-search" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','');">
                <button id="parser-search-button"><i class="fa-light fa-magnifying-glass"></i></button>
            </div>
        </div>

    </div>
    <div class="title-page">
        <h1>Каталог товаров NORDIHOME</h1>
    </div>
    <div class="row">
        @foreach($categories as $category)
            @include('shop.nordihome.parser.cards.catalog', ['category' => $category])
        @endforeach
    </div>

    <script type="application/ld+json" class="schemantra.com">

    </script>
@endsection
