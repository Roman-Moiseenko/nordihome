<!DOCTYPE html>

<html class="light default" lang="ru-RU" >
<!-- BEGIN: Head -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>NORDI HOME - CRM</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

@yield('head')

<!-- BEGIN: CSS Assets-->
    @vite('resources/css/admin.css')
    @vite('resources/js/admin.js')
@stack('styles')
<!-- END: CSS Assets-->
</head>
<!-- END: Head -->

<body class="@yield('body')">
@yield('content')
@include('flash::message')


<!-- BEGIN: Vendor JS Assets-->
@stack('vendors')
<!-- END: Vendor JS Assets-->

<!-- BEGIN: Pages, layouts, components JS Assets-->
@stack('scripts')
<!-- END: Pages, layouts, components JS Assets-->
<script type="text/javascript">
    /* скрываем окно сообщения ч/з 3 сек */
</script>
</body>

</html>
