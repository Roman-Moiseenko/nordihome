@extends('shop.nordihome.layouts.main')

@section('body', 'category parser')
@section('main', 'container-xl catalogs-page')
@section('title', $title)
@section('description', $description)


@section('content')
<div class="title-page">
    <h1>Каталог товаров NORDIHOME</h1>
</div>
    <div class="row">
        @foreach($categories as $category)
            @include('shop.nordihome.parser.cards.catalog', ['category' => $category])
        @endforeach
    </div>

<script type="application/ld+json" class="schemantra.com">

</script>
@endsection
