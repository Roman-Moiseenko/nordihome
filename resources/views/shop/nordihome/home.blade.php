@extends('shop.nordihome.layouts.main')

@section('breadcrumbs')
@endsection

@section('main', 'home')

@section('content')
    <div class="container-xl mt-5">
        <h1>САЙТ НАХОДИТСЯ В РАЗРАБОТКЕ 1</h1>
        <h2>Весь ассортимент товара, количество, цена являются вымешенными и не имеют отношение к реальной продажи</h2>
    </div>

    @include('shop.nordihome.old_widget.spec')
    @include('shop.nordihome.old_widget.interes')
    @include('shop.nordihome.old_widget.vibirat')
    @include('shop.nordihome.old_widget.vnimanie')
    @include('shop.nordihome.old_widget.dostavka')
    @include('shop.nordihome.old_widget.nalich-i-zakaz')
    @include('shop.nordihome.old_widget.otzivi')
    @include('shop.nordihome.old_widget.voprosi')
    @include('shop.nordihome.old_widget.nalich-i-zakaz2')
    <livewire:shop.widget.feedback />


    <div class="mt-3">
        @include('shop.nordihome.widgets.map')
    </div>
    {!! '$schema->HomePage()' !!}
@endsection
