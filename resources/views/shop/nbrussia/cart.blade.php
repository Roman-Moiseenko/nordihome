@extends('shop.nbrussia.layouts.main')

@section('body', 'product')
@section('main', 'container-xl cart-page')
@section('title', 'Корзина товаров в Интернет-магазине NB Russia')

@section('content')
    <livewire:n-b-russia.cart.page :user="$user" />
@endsection


