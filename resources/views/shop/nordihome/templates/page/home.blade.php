<!--template:Пустая Главная-->
@extends('shop.nordihome.layouts.main')

@section('breadcrumbs')
@endsection

@section('main', 'home')

@section('title', $title)
@section('description', $description)

@section('content')
    {!! $page->text !!}
@endsection
