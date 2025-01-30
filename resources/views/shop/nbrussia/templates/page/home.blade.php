<!--template:Пустая Главная-->
@extends('shop.nbrussia.layouts.main')

@section('breadcrumbs')
@endsection

@section('main', 'home container-xl')

@section('title', $title)
@section('description', $description)

@section('content')
    {!! $page->text !!}
@endsection
