@extends('layouts.shop')

@section('body', 'page')
@section('main', 'container-xl')
@section('title', $page->title)
@section('description', $page->description)

@section('content')
    @yield('subcontent')
@endsection
