@php
    use App\Modules\Shop\Application\DTOs\Pages\PostViewPageData;
    /** @var PostViewPageData  $pageData */
@endphp
@extends('shop.layouts.main')

@section('main', 'posts container-xl')

@section('title', $pageData->meta->title)
@section('description', $pageData->meta->description)

@section('content')
    @foreach($pageData->blocks as $block)
        <div class="widget mt-4">
            @include('widgets::' . $block->widget->category . '.' . $block->widget->slug, ['params' => $block->widget->params])
        </div>
    @endforeach
@endsection

@section('bottom-content')

@endsection
