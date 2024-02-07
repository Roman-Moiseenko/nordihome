@extends('layouts.shop')

@section('body')
    category
@endsection

@section('main')
    container-xl catalogs-page
@endsection

@section('title', $title)
@section('description', $description)

@section('content')
<div class="title-page">
    {{ $category->name }}
</div>
    <div>
        @foreach($category->children as $_child)
            <div>
                <img src="{{ $_child->getImage() }}" width="100px">
                <a href="{{ route('shop.category.view', $_child->slug) }}">{{ $_child->name }}</a>
            </div>
        @endforeach
    </div>


@endsection
