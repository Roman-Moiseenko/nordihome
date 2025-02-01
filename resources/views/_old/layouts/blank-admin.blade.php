<!DOCTYPE html>

<html class="" lang="ru-RU">
<!-- BEGIN: Head -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'CRM')</title>

    <!-- Fonts -->

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<!-- BEGIN: CSS Assets-->
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
<!-- END: CSS Assets-->
</head>
<!-- END: Head -->

<body>
@yield('content')

</body>

</html>
