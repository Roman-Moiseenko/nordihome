@extends('layouts.shop')

@section('body')
    product
@endsection

@section('main')
    container-xl order-page
@endsection

@section('content')
    <div class="title-page">
        <h1>Оформление заказа</h1>
    </div>
    <div class="d-flex">
        <div class="left-list-block">
            <div class="box-card">
                Способы оплаты
            </div>
            <div class="box-card">
                Доставка/самовывоз
            </div>
            <div class="box-card">
                Список товаров в корзине
            </div>
        </div>
        <div class="right-action-block">
            <div>
                Оплатить / Сохранить<br>
                Промокоды и купоны
            </div>
        </div>
    </div>
@endsection
