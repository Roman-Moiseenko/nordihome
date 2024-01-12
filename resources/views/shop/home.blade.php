@extends('layouts.shop')

@section('breadcrumbs')
@endsection

@section('content')

<div class="container-xl">

    @foreach($widgets as $widget)
    <div class="shop-home-widget">
        @if(!empty($widget['name']))<h2 class="fs-4">{{ $widget['name'] }}</h2>@endif
        @include('shop.widgets.home.' . $widget['widget'], ['items' => $widget['items']])
    </div>
    @endforeach

</div>
@endsection
