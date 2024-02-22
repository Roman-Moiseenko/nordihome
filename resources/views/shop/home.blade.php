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

    @include('shop.old_widget.spec')
    @include('shop.old_widget.interes')
    @include('shop.old_widget.vibirat')
    @include('shop.old_widget.vnimanie')
    @include('shop.old_widget.dostavka')
    @include('shop.old_widget.nalich-i-zakaz')
    @include('shop.old_widget.otzivi')
    @include('shop.old_widget.voprosi')
    @include('shop.old_widget.nalich-i-zakaz2')
    @include('shop.widgets.contact')

    <div class="mt-3">
        @include('shop.widgets.map')
    </div>
    {!! $schema->HomePage() !!}
@endsection
