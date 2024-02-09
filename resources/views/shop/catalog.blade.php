@extends('layouts.shop')

@section('body')
    category
@endsection

@section('main')
    container-xl catalogs-page
@endsection

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
    {{ json_encode($schema->CategoryPage($category)) }}
</script>
@endsection
