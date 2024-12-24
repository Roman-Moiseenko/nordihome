@extends('layouts.shop')

@section('body', 'category')
@section('main', 'container-xl catalogs-page')
@section('title', $title)
@section('description', $description)

@section('content')
<div class="title-page">
    <h1>Каталог товаров NORDIHOME</h1>
</div>
    <div class="row">
        @foreach($categories as $category)
            @include('shop.cards.catalog', ['category' => $category])
        @endforeach
    </div>

<script type="application/ld+json" class="schemantra.com">
    {{ json_encode($schema->CategoryPage($category['id'])) }}
</script>
@endsection
