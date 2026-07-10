@extends('shop.layouts.main')

@section('body', 'category')
@section('main', 'container-xl categorys-page')
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

</script>
@endsection
