<!--template:Пустая Главная-->
@extends('shop.nordihome.layouts.main')

@section('breadcrumbs')
@endsection

@section('main', 'home container-xl')

@section('title', $title)
@section('description', $description)

@section('content')
    *****
    {!! $page->text !!}
@endsection

@pushonce('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.css"/>
@endpushonce
@pushonce('script')
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.umd.js"></script>
@endpushonce
