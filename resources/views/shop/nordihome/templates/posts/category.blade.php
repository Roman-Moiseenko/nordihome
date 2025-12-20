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
<div>
    {{ $post->title }}
    <a href="{{ route('shop.post.view', $post->slug) }}">{{ $post->name }}</a>
    <div class="content">
        {!! $post->getParagraphs(2) !!}
    </div>
</div>
@endforeach
<div class="products-page-list--bottom">
    {{ $posts->links('shop.nordihome.widgets.paginator') }}
</div>
@endsection
