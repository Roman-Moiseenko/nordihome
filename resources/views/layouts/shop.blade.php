<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!--base href="https://39y.ru" /-->
    <meta name="robots" content="noindex" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ url('images/favicon/32x32.png') }}" size="32x32">
    <link rel="icon" href="{{ url('images/favicon/192x192.png') }}" size="192x192">
    <link rel="apple-touch-icon" href="{{ url('images/favicon/180x180.png') }}" size="192x192">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'NORDY HOME - Интернет магазин товаров из Европы')</title>
    <meta name="description" content="@yield('description', 'Интернет магазин товаров из Европы, с доставкой почтой и ТК по России')">

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>


@yield('head')
    <!-- Scripts -->
    @vite(['resources/sass/shop.scss', 'resources/js/shop.js'])
    @stack('styles')
</head>
<body class="@yield('body')">



@include('shop.header')
@include('flash::message')

@section('breadcrumbs')
    <div class="container-xl">
        {{ \Diglactic\Breadcrumbs\Breadcrumbs::view('partials.breadcrumbs-shop') }}
    </div>
@show

<main class="@yield('main')">
    @yield('content')
</main>
<!--POP-UP ОКНА-->
@guest
    @include('shop.pop-up.login')
@endguest
@include('shop.pop-up.buy-click')
<!--FOOTER-->
@include('shop.footer')
<button id="upbutton" type="button" class="scrollup" aria-label="В начало"><i class="fa fa-arrow-up"></i></button>
@stack('scripts')
</body>
</html>
