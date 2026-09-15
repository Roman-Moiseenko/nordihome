<!--template:Пустая Главная-->
@php
    use App\Modules\Shop\Application\DTOs\Pages\PageViewPageData;
    /** @var PageViewPageData $pageData */
@endphp
@extends('layouts.main')
@section('breadcrumbs')
@endsection
@section('main', 'home')

@section('content')
    {!! $pageData->text !!}
    @include('shop.widgets.map')
@endsection

@pushonce('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.css"/>
@endpushonce
@pushonce('script')
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.umd.js"></script>
@endpushonce
