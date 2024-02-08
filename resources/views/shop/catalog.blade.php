@extends('layouts.shop')

@section('body')
    category
@endsection

@section('main')
    container-xl catalogs-page
@endsection

@section('content')
<div class="title-page">
    Каталог
</div>
    <div>
        @foreach($categories as $category)
            <div>
                <img src="{{ $category->getImage() }}" width="100px">
                <a href="{{ route('shop.category.view', $category->slug) }}">{{ $category->name }}</a>
            </div>
        @endforeach
    </div>

<script type="application/ld+json" class="schemantra.com">
    {{ json_encode($schema->CategoryPage($category)) }}
</script>
@endsection
