<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!--base href="https://39y.ru" /-->
    <meta name="robots" content="noindex"/>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ url('images/nbrussia/favicon/32x32.png') }}" size="32x32">
    <link rel="icon" href="{{ url('images/nbrussia/favicon/192x192.png') }}" size="192x192">
    <link rel="apple-touch-icon" href="{{ url('images/nbrussia/favicon/180x180.png') }}" size="192x192">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'New Balance - Поставка оригинальных товаров из Европы')</title>
    <meta name="description"
          content="@yield('description', 'Интернет магазин товаров из Европы, с доставкой почтой и ТК по России')">

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    @livewireStyles
    @yield('head')
    <!-- Scripts -->
    @vite(['resources/sass/nbrussia.scss', 'resources/js/nbrussia.js'])
    @stack('styles')
</head>
<body class="@yield('body')">
@include('shop.nbrussia.header')
@include('shop.nbrussia.widgets.flash')

@section('breadcrumbs')
    <div class="container-xl">
        {{ \Diglactic\Breadcrumbs\Breadcrumbs::view('shop.nbrussia.breadcrumbs') }}
    </div>
@show

<main class="@yield('main')">
    @yield('content')
</main>
<!--POP-UP ОКНА-->
@guest
    @include('shop.nbrussia.pop-up.login')
@endguest

@include('shop.nbrussia.pop-up.buy-click')
@include('shop.nbrussia.pop-up.notification')

<!--FOOTER-->
@include('shop.nbrussia.footer')
<button id="upbutton" type="button" class="scrollup" aria-label="В начало"><i class="fa fa-arrow-up"></i></button>
@stack('scripts')

@livewireScripts
</body>
</html>
