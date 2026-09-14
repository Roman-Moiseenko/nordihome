@extends('cabinet.layout')
@section('body')
    @parent
    wish
@endsection
@section('h1', 'Избранное')

@section('subcontent')
    <livewire:cabinet.wish.wish-page />
@endsection
