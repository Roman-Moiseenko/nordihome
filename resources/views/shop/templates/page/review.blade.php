@php
    use App\Modules\Shop\Application\DTOs\Pages\PageViewPageData;
    /** @var PageViewPageData $pageData */
@endphp
@extends('layouts.main')
@section('main', 'container-xl pages')

@section('content')
    <div class="container-xl">
        <h1 class="my-4">{{ $pageData->name }}</h1>
        <div class="">
            Отзывы
        </div>
    </div>
@endsection
