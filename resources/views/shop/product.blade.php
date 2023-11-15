@extends('layouts.shop')

@section('body')
    product
@endsection

@section('main')
    container-xl
@endsection

@section('content')

    <div>
        {{ $product->name }}
    </div>

@endsection
