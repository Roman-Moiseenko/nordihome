@extends('layouts.shop')

@section('body', 'promotions')

@section('main', 'container-xl products-page')
@section('title', $title)
@section('description', $description)

@section('content')
    <div class="title-page">
        <div class="products-page-title d-flex h1">
            <h1>{{ $promotion->title }} </h1>
            <span>&nbsp;{{ \App\Modules\Shop\Helper::_countProd(count($products)) }} </span>
        </div>
    </div>

    <div class="row">
        @foreach($products as $product)
            @include('shop.cards.card-4x')
        @endforeach
    </div>
@endsection
