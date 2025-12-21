<!--template:Запись тест-->
@php
    /** @var \App\Modules\Page\Entity\Post $post */
    $photo = photo_std('main_001', 'card');
@endphp
@extends('shop.nordihome.layouts.main')

@section('main')
    post container-xl
@endsection

@section('title', $title)
@section('description', $description)

@section('content')
    <h1 class="my-4">{{ $post->title }}</h1>
    <img src="{{ $post->getImage('catalog') }}" />
    <div class="mt-4">
        {{ $post->description }}
    </div>

    <div>
        {!! $post->text !!}
    </div>

    <div>
        <h3>{{ $photo->title }}</h3>
        <img src="{{ $photo->url }}" alt="{{ $photo->alt }}">
        <div>
            {{ $photo->description }}
        </div>
    </div>

@endsection
