@php
    use App\Modules\Shop\Application\DTOs\Pages\PostViewPageData;
    /** @var PostViewPageData  $pageData */
@endphp
@extends('shop.layouts.main')

@section('main', 'posts container-xl')

@section('title', $pageData->meta->title)
@section('description', $pageData->meta->description)

@section('content')
    <h1 class="my-4">{{ $pageData->title }}</h1>

    @foreach($pageData->blocks as $block)
        @include('widgets::' . $block->widget->category . '.' . $block->widget->slug, ['params' => $block->widget->params])
    @endforeach
@endsection
