@php

    use App\Modules\Shop\Application\DTOs\Pages\PostIndexPageData;

    /** @var \App\Modules\Content\Entity\PostCategory $category */
    /** @var \App\Modules\Content\Infrastructure\Models\Post[] $posts */
    /** @var PostIndexPageData $pageData */
    /**
    * $category->getImage();
    * $category->getIcon();
    * $post->getParagraphs() - первые абзацы из текста
    *
    */
@endphp
@extends('shop.layouts.main')

@section('main')
    posts container-xl
@endsection

@section('title', $pageData->meta->title)
@section('description', $pageData->meta->description)

@section('content')
    <h1 class="my-4">{{ $pageData->category->title }}</h1>
    <div class="mt-4">
        {{ $pageData->category->description }}
    </div>

    @foreach($pageData->posts as $post)
        <div class="item-news m-b_20">
            <div class="row">
                <div class="col-md-5 col-lg-4 img">
                    <img src="{{ $post->image }}" alt="{{ $post->caption }}" class="width_100">
                </div>
                <div class="col-md-7 col-lg-8">
                    <h3 class="news-head"><a href="{{ route('shop.post.view', $post->slug) }}">{{ $post->caption }}</a>
                    </h3>
                    <div class="news-info">
                        {{ $post->fragment }}
                    </div>
                    <div class="news-more-link m-t_20">
                        <a href="{{ route('shop.post.view', $post->slug) }}" class="btn btn-orange">Подробнее</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <div class="products-page-list--bottom">
        @include('shop.widgets.paginator', ['paginator' => $pageData->paginator])
    </div>
    <script type="application/ld+json" class="schemantra.com">
        {!! json_encode($pageData->schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection
