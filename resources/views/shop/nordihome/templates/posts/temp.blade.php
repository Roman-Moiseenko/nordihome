<!--template:Записи основной-->
@php

    /** @var \App\Modules\Page\Entity\PostCategory $category */
    /** @var \App\Modules\Page\Entity\Post[] $posts */
@endphp
@extends('shop.nordihome.layouts.main')

@section('main')
    posts container-xl
@endsection

@section('title', $title)
@section('description', $description)

@section('content')
    <h1 class="my-4">{{ $category->title }}</h1>
    <div class="mt-4">
        {{ $category->description }}
    </div>

    @foreach($posts as $post)
        <div>
            {{ $post->title }} {{ $widget->getImage() }}
            <a href="{{ route('shop.post.view', $post->slug) }}">{{ $post->name }}</a>
        </div>
    @endforeach
    <div class="products-page-list--bottom">
        {{ $posts->links('shop.nordihome.widgets.paginator') }}
    </div>
@endsection
