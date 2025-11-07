<!--template:О компании-->
@extends('shop.nordihome.layouts.main')

@section('main')
    pages
@endsection

@section('title', $title)
@section('description', $description)

@section('content')
    <div class="container-xl">
        <h1 class="my-4">{{ $page->name }}</h1>
        <div class="row">
            <div class="col-lg-6">66</div>
            <div class="col-lg-6"><img src="/images/pages/about/bg-sl-12.jpg" alt="о компании Nordi home"></div>
        </div>
    </div>
    <div class="container-xl">
        <div class="mt-4">
            {!! $page->text !!}
        </div>
    </div>
@endsection
