@extends('layouts.shop')

@section('body')
    category
@endsection

@section('main')
    container-xl
@endsection

@section('content')
<div>
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
