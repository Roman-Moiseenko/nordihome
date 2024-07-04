@extends('layouts.shop')

@section('body', 'product')
@section('main', 'container-xl cart-page')
@section('title', 'Корзина товаров в Интернет-магазине NORDI HOME')

@section('content')

    <livewire:cabinet.cart.cart-page :user="$user"/>
@endsection


