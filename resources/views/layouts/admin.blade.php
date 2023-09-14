<!DOCTYPE html>

<html class="light" lang="ru-RU">
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
    @vite('resources/sass/admin.scss')
@stack('styles')
<!-- END: CSS Assets-->
</head>
<!-- END: Head -->

<body class="@yield('body')">
@yield('content')

@vite('resources/js/admin.js')

<!-- BEGIN: Vendor JS Assets-->
@stack('vendors')
<!-- END: Vendor JS Assets-->

<!-- BEGIN: Pages, layouts, components JS Assets-->
@stack('scripts')
<!-- END: Pages, layouts, components JS Assets-->
<script type="text/javascript">
    /* скрываем окно сообщения ч/з 3 сек */
    setTimeout(() => {
        let _alert = document.querySelector('div.alert');
        _alert.style.display = "none";
    }, 3000);
</script>
</body>

</html>
