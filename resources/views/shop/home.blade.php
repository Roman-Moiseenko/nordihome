@extends('layouts.shop')

@section('breadcrumbs')
@endsection

@section('main', 'home')

@section('content')

    @foreach($widgets as $widget)
        {!! $widget->view() !!}
    @endforeach

    <div class="mt-3">
        @include('shop.widgets.map')
    </div>
    {!! $schema->HomePage() !!}
@endsection
