<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ url('/images/favicon.png') }}" size="32x32">
    <!--link rel="icon" href="  Vite::asset('resources/images/admin/192x192.png')}}" size="192x192">
    <link rel="apple-touch-icon" href="  Vite::asset('resources/images/admin/180x180.png')}}" size="180x180"-->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.0.2/vue.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.10.2/Sortable.min.js"></script>
    <!--script src="//cdn.jsdelivr.net/npm/@element-plus/icons-vue"></script-->
    <title inertia>{{ 'SMAP&Shop' }}</title>
    @routes
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
    <!-- -->
    @inertiaHead
</head>
<body class="">
@inertia
@stack('vendors')
@stack('scripts')
</body>
</html>
