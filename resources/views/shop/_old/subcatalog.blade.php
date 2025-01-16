@extends('layouts.shop')

@section('body', 'category')
@section('main', 'container-xl catalogs-page')
@section('title', $title)
@section('description', $description)

@section('content')
    <div class="title-page">
        <h1>{{ $category->name }}</h1>
    </div>
    <div class="row">
        @foreach($children as $category)
            @include('shop.cards.catalog', ['category' => $category])
        @endforeach
    </div>

    <script type="application/ld+json" class="schemantra.com">
        {{ json_encode($schema->CategoryPage($category['id'])) }}
    </script>
@endsection
