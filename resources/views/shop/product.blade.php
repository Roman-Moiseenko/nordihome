@extends('layouts.shop')

@section('body')
    product
@endsection

@section('main')
    container-xl product-page
@endsection

@section('content')

    <div class="title-page">
        {{ $product->name }}
    </div>

@endsection
