@extends('layouts.shop')

@section('body')
    category
@endsection

@section('main')
    container-xl
@endsection

@section('content')

    {{ $category->name }}

@endsection
