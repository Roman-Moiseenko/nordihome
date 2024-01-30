<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!--base href="https://39y.ru" /-->
    <meta name="robots" content="noindex" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>NORDI HOME - SHOP</title>

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
@include('shop.footer')
<button id="upbutton" type="button" class="scrollup" aria-label="В начало"><i class="fa fa-arrow-up"></i></button>
@stack('scripts')
</body>
</html>
