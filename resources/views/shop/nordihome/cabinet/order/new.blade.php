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
            Менеджер свяжется с вами в течении .... минут, для обсуждения деталей
        </div>
        <div class="right-action-block">
            <div class="sticky-block">


            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Диспатчим кастомное событие, когда Blade-скрипт готов
            window.dispatchEvent(new CustomEvent('e-order', {
                detail: {!! json_encode($e_array) !!}
            }));
        });

    </script>
@endsection
