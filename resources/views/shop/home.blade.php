@extends('layouts.shop')

@section('breadcrumbs')
@endsection

@section('main', 'home')

@section('content')
    <div class="container-xl mt-5">
        <h1>САЙТ НАХОДИТСЯ В РАЗРАБОТКЕ</h1>
        <h2>Весь ассортимент товара, количество, цена являются вымешенными и не имеют отношение к реальной продажи</h2>
    </div>
    @foreach($widgets as $widget)
        {!! $widget->view() !!}
    @endforeach

    <div class="mt-3">
        @include('shop.widgets.map')
    </div>
    {!! $schema->HomePage() !!}
@endsection
