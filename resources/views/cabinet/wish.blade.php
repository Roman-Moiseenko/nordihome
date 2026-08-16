@extends('cabinet.layout')
@section('body')
    @parent
    wish
@endsection

@section('title', 'Мои избранные товары - NORDI HOME')
@section('h1', 'Избранное')

@section('subcontent')
    <livewire:cabinet.wish.wish-page :client-id="$client->id"/>
@endsection
