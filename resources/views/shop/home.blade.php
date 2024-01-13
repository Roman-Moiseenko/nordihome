@extends('layouts.shop')

@section('breadcrumbs')
@endsection

@section('content')

<div class="container-xl">

    @foreach($widgets as $widget)
        {!! $widget->view() !!}
    @endforeach

</div>
@endsection
