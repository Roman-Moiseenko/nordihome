@extends('shop.nbrussia.layouts.main')

@section('body', 'category')
@section('main', 'container-xl catalogs-page')
@section('title', $title)
@section('description', $description)


@section('content')
    <div class="title-page">
        <h1>Каталог товаров New Balance</h1>
    </div>
    <div class="row">
        @foreach($categories as $category)
            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                <div class="catalog-card">
                    <a href="{{ route('shop.category.view', $category['slug']) }}">
                        <div>
                            <img
                                src="{{ (is_null($category['image'])) ? '\images\no-image.jpg' : $category['image'] }}">
                            <span>{{ $category['name'] }}</span>
                        </div>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <script type="application/ld+json" class="schemantra.com">
        {{ json_encode($schema->CategoryPage($category['id'])) }}
    </script>
@endsection
