@extends('cabinet.cabinet')
@section('body')
    @parent
    wish
@endsection

@section('title', 'Мои избранные товары - NORDI HOME')
@section('h1', 'Избранное')

@section('subcontent')
    <livewire:cabinet.wish.wish-page :user="$user"/>
@endsection
