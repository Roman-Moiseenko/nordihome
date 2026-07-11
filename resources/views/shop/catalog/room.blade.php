@extends('shop.layouts.main')

@section('body', 'room')
@section('main', 'container-xl rooms-page')
@section('title', $title ?? '')
@section('description', $description ?? '')


@section('content')
    <div class="title-page">
        <h1>Каталог товаров NORDIHOME</h1>
    </div>
    <div class="row">
        @foreach($rooms as $room)
            @include('shop.catalog.card-room', ['room' => $room])
        @endforeach
    </div>

    <script type="application/ld+json" class="schemantra.com">

    </script>
@endsection
