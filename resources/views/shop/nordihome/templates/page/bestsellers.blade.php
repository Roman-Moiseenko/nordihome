<!--template:Страница Хиты продаж -->
@extends('shop.nordihome.layouts.main')

@section('main')
    pages
@endsection

@section('title', $title)
@section('description', $description)

@section('content')
    <div class="container-xl">
        <h1 class="my-4">{{ $page->name }}</h1>
        <p>Здесь должен быть топ-10 товаров из ИКЕА, но мы с клиентами и командой НОРДИ ХОУМ не смогли определиться. Поэтому создали топ-11. На самом деле, хитов еще больше, но эти – наши фавориты! Они универсальны, имеют отличное качество и помогают создать стильный интерьер в любом доме.</p>
    </div>
    <div class="mt-4">
        {!! $page->text !!}
    </div>

@endsection
