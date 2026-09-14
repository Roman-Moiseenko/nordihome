<!--template:Страница контакты-->
@php
    use App\Modules\Shop\Application\DTOs\Pages\PageViewPageData;
    /** @var PageViewPageData $pageData */
@endphp
@extends('layouts.main')
@section('main', 'pages')

@section('content')
        <div class="container-xl"><h1 class="my-4">{{ $pageData->name }}</h1></div>
        <div class="mt-4">
            {!! $pageData->text !!}
        </div>
@endsection
