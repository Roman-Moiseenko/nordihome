<!--template:Запись шаблон по-умолчанию-->
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
    <div class="mt-4">
        {{ $post->description }}
    </div>

    <div>
        <img src="{{ $post->getImage('post') }}" alt="{{ $post->title }}" class="img-alignright" width="450">
        {!! $post->text !!}
    </div>
@endsection
