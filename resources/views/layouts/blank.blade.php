<!DOCTYPE html>

<html class="" lang="ru-RU">
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


<!-- BEGIN: CSS Assets-->
    @vite('resources/sass/admin.scss')
<!-- END: CSS Assets-->
</head>
<!-- END: Head -->

<body>
@yield('content')
@vite('resources/js/admin.js')
</body>

</html>
