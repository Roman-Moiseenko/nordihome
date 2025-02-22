<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!--base href="https://nbrussia.ru" /-->

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ url('images/nbrussia/favicon/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ url('images/nbrussia/favicon/32x32.png') }}" size="32x32">
    <link rel="icon" href="{{ url('images/nbrussia/favicon/192x192.png') }}" size="192x192">
    <link rel="apple-touch-icon" href="{{ url('images/nbrussia/favicon/180x180.png') }}" size="192x192">
    <link rel="canonical" href="@yield('canonical', $url_page)">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'New Balance - Поставка оригинальных товаров из Европы')</title>
    <meta name="description"
          content="@yield('description', 'Интернет магазин товаров из Европы, с доставкой почтой и ТК по России')">

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- Yandex.Metrika counter -->
    @if(!empty($web->metrika))
    <script type="text/javascript" >
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym({{ $web->metrika }}, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true,
            webvisor:true
        });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/{{ $web->metrika }}" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    @endif
    <!-- /Yandex.Metrika counter -->


    @livewireStyles
    @yield('head')
    <!-- Scripts -->
    @vite(['resources/sass/nbrussia.scss', 'resources/js/nbrussia.js'])
    @stack('styles')

    <livewire:shop.c-s-r-f />
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

@include('shop.nbrussia.pop-up.new-order')
@include('shop.nbrussia.pop-up.notification')

<!--FOOTER-->

@include('shop.nbrussia.footer')
@include('shop.nbrussia.widgets.contacts')
<button id="upbutton" type="button" class="scrollup" aria-label="В начало"><i class="fa fa-arrow-up"></i></button>
@stack('scripts')

@livewireScripts
</body>
</html>
