<!--template:Запись шаблон по-умолчанию-->
@php
    /** @var \App\Modules\Page\Entity\Post $post */
    $photo = photo_std('main_001', 'card');
@endphp
@extends('shop.nordihome.layouts.main')

@section('main')
    post
@endsection

@section('title', $title)
@section('description', $description)

@section('content')
    <div class="container-xl">
        <h1 class="my-4">{{ $post->title }}</h1>
    </div>
    <div>
        {!! $post->text !!}
    </div>
@endsection
