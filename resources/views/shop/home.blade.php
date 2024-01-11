@extends('layouts.shop')

@section('breadcrumbs')
@endsection

@section('content')

<div class="container-xl">

    @foreach($widgets as $widget)
    <div class="shop-home-widget">
        @if(!empty($widget['name']))<h2>{{ $widget['name'] }}</h2>@endif
        @include('shop.widgets.home.' . $widget['widget'], ['items' => $widget['items']])
    </div>
    @endforeach
    <div>
        Блок 2

    </div>
    <div>
        Блок 3

    </div>


    <i class="fa fa-home"></i>
    <i class="fa-brands fa-square-whatsapp"></i>
    <div class="row">
        <div class="col-6">
            <i class="fa-regular fa-pen-to-square"></i> Левая половина
        </div>
        <div class="col-6">
            <div class="float-end">
            Правая половина <i class="fa-light fa-print-magnifying-glass"></i>
            </div>
        </div>
    </div>

</div>
@endsection
