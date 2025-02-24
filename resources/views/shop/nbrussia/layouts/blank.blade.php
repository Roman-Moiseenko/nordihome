<!DOCTYPE html>

<html class="" lang="ru-RU">
<!-- BEGIN: Head -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ url('images/nbrussia/favicon/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ url('images/nbrussia/favicon/32x32.png') }}" size="32x32">
    <link rel="icon" href="{{ url('images/nbrussia/favicon/192x192.png') }}" size="192x192">
    <link rel="apple-touch-icon" href="{{ url('images/nbrussia/favicon/180x180.png') }}" size="192x192">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Восстановление доступа к личному кабинету')</title>

    <!-- Fonts -->



<!-- BEGIN: CSS Assets-->
    @vite(['resources/sass/nbrussia.scss', 'resources/js/nbrussia.js'])
    @stack('styles')
<!-- END: CSS Assets-->
</head>
<!-- END: Head -->

<body class="@yield('body')">
<main class="@yield('main')">
    @yield('content')
</main>

@include('shop.nbrussia.footer')
@stack('scripts')
</body>

</html>
