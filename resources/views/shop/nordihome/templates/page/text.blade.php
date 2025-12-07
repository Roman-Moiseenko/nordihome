<!--template:Обычная текстовая страница-->
@extends('shop.nordihome.layouts.main')

@section('main')
    pages
@endsection

@section('title', $title)
@section('description', $description)

@section('content')
    <h1 class="my-4">{{ $page->name }}</h1>
    <div class="mt-4">
        {!! $page->text !!}
    </div>
@endsection
