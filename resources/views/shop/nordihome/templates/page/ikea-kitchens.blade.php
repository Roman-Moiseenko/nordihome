<!--template:-->
@extends('shop.nordihome.layouts.main')

@section('main')
    pages
@endsection

@section('title', $title)
@section('description', $description)

@section('content')
    <div class="container-xl">
        <h1 class="my-4">{{ $page->name }}</h1>
    </div>
    <div class="container-xl">
        <div class="mt-4">
            {!! $page->text !!}
        </div>
    </div>
@endsection
