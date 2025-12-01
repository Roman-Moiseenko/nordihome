@extends('shop.nordihome.layouts.main')

@section('body', 'order')
@section('main', 'container-xl order-page-create')
@section('title', 'Заказ сформирован')

@section('content')
    <div class="title-page">
        <h1>Заказ сформирован</h1>
    </div>
    <div class="screen-action">
        <div class="left-list-block">

        </div>
        <div class="right-action-block">
            <div class="sticky-block">

            </div>
        </div>
    </div>

    <script>

        console.log({{$e_array}})
        console.log({{ json_encode($e_array) }})
        // Create the event
        let event = new CustomEvent("e-order", { "detail": {{ json_encode($e_array) }} });

        // Dispatch/Trigger/Fire the event
        window.dispatchEvent(event);

    </script>
@endsection
