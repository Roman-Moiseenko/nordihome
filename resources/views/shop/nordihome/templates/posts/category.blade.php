<!--template:Категории шаблон по-умолчанию-->
@php

/** @var \App\Modules\Page\Entity\PostCategory $category */
/** @var \App\Modules\Page\Entity\Post[] $posts */
/**
* $category->getImage();
* $category->getIcon();
* $post->getParagraphs() - первые абзацы из текста
*
*/
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
    <div class="item-news m-b_20">
        <div class="row">
            <div class="col-md-5 col-lg-4 img">
                <img src="{{ $post->getImage('post') }}" alt="{{ $post->title }}" class="width_100">
            </div>
            <div class="col-md-7 col-lg-8">
                <h3 class="news-head"><a href="{{ route('shop.post.view', $post->slug) }}">{{ $post->name }}</a></h3>
                <div class="news-info">
                    {{ $post->description }}
                </div>
                <div class="news-more-link m-t_20">
                    <a href="{{ route('shop.post.view', $post->slug) }}" class="btn btn-orange">Подробнее</a>
                </div>
            </div>
        </div>
    </div>
@endforeach
<div class="products-page-list--bottom">
    {{ $posts->links('shop.nordihome.widgets.paginator') }}
</div>
@endsection
