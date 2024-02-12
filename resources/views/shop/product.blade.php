@extends('layouts.shop')

@section('body', 'product')
@section('main', 'container-xl product-page')
@section('title', $title)
@section('description', $description)

@section('content')

    <div class="title-page">
        {{ $product->name }}
    </div>

    {!! $schema->ProductPage($product) !!}
@endsection
