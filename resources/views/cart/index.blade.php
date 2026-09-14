@extends('layouts.main')
@section('body', 'product')
@section('main', 'container-xl cart-page')

@section('content')

    <livewire:cabinet.cart.cart-page  :client-id="$client?->id ?? null"/>
@endsection


