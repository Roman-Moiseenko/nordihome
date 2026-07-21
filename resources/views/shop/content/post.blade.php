@php
    use App\Modules\Shop\Application\DTOs\Pages\PostViewPageData;
    /** @var PostViewPageData  $pageData */
@endphp
@extends('shop.layouts.main')
@section('title', $pageData->meta->title)
@section('description', $pageData->meta->description)
@section('body', 'post')

@section('main', 'container-xl')
@section('content')
    @foreach($pageData->blocks as $block)
        @if($block->section == "content")
            <div class="widget mt-4">
                @include('widgets::' . $block->widget->category . '.' . $block->widget->slug,
                [
                    'params' => $block->widget->params,
                    'widget' => $block->widget->id,
                ])
            </div>
        @endif
    @endforeach

    <script type="application/ld+json" class="schemantra.com">
        {!! json_encode($pageData->schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('bottom-content')
    @foreach($pageData->blocks as $block)
        @if($block->section == "bottom-content")
            <div class="widget mt-4">
                @include('widgets::' . $block->widget->category . '.' . $block->widget->slug,
                [
                    'params' => $block->widget->params,
                    'widget' => $block->widget->id,
                ])
            </div>
        @endif
    @endforeach
@endsection
