@php
    use App\Modules\Shop\Application\DTOs\CategoryRoomIndexPageData;
    /** @var CategoryRoomIndexPageData $pageData */
@endphp

@extends('shop.layouts.main')

@section('body', 'room')
@section('main', 'container-xl rooms-page')
@section('title', $pageData->meta->title)
@section('description', $pageData->meta->description)


@section('content')
    <div class="title-page">
        <h1>Каталог товаров по комнатам</h1>
    </div>
    <div class="row">
        @foreach($pageData->categories as $room)
            @include('shop.catalog.card', ['item' => $room])
        @endforeach
    </div>

    <script type="application/ld+json" class="schemantra.com">

    </script>
@endsection
