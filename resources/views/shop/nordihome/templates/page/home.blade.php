<!--template:Пустая Главная-->
@extends('shop.nordihome.layouts.main')

@section('breadcrumbs')
@endsection

@section('main', 'home container-xl77')

@section('title', $title)
@section('description', $description)

@section('content')
    {!! $page->text !!}
@endsection
