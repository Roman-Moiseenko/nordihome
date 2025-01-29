<!DOCTYPE html>

<html class="" lang="ru-RU">
<!-- BEGIN: Head -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Восстановление доступа к личному кабинету')</title>

    <!-- Fonts -->



<!-- BEGIN: CSS Assets-->
    @vite(['resources/sass/nbrussia.scss', 'resources/js/nbrussia.js'])
<!-- END: CSS Assets-->
</head>
<!-- END: Head -->

<body>
@yield('content')

</body>

</html>
