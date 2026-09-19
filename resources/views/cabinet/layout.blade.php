@php
    $cabinetMenus = [
            'cabinet' => [
                'name' => 'Личный кабинет',
                'icon' => 'fa-light fa-user-vneck',
                'url' => route('cabinet.view'),
            ],
            'orders' => [
                'name' => 'Мои заказы',
                'icon' => 'fa-sharp fa-light fa-box-open',
                'url' => route('cabinet.order.index'),
            ],
            'wish' => [
                'name' => 'Избранное',
                'icon' => 'fa-light fa-heart',
                'url' => route('cabinet.wish.index'),
            ],
            'cart' => [
                'name' => 'Корзина',
                'icon' => 'fa-light fa-cart-shopping',
                'url' => route('shop.cart.view'),
            ],
            'review' => [
                'name' => 'Мои отзывы',
                'icon' => 'fa-sharp fa-light fa-message-smile',
                'url' => route('cabinet.review.index'),
            ],
            'options' => [
                'name' => 'Настройки',
                'icon' => 'fa-light fa-user-gear',
                'url' => route('cabinet.options.index'),
            ],
            'logout' => [
                'name' => 'Выход',
                'icon' => 'fa-light fa-right-from-bracket',
                'url' => route('logout'),
            ],
        ];
@endphp

@extends('layouts.main')
@section('body', 'cabinet')
@section('main', 'container-xl cabinet')

@section('content')
    <div class="title-page">
        <h1>@yield('h1')</h1>
    </div>
    <div class="screen-action">
        <div class="left-list-block">
            @yield('subcontent')
        </div>
        <div class="right-action-block">
            <div class="sticky-block">
                <div class="cabinet-menu-block">
                    <ul class="cabinet-menu">
                        @foreach($cabinetMenus as $item)
                            <li class="cabinet-menu-item {{ ($item['url'] == request()->url()) ? 'active' : '' }}">
                                <a href="{{ $item['url'] }}">
                                    <i class="{{ $item['icon'] }}"></i>
                                    <span>{{ $item['name'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
