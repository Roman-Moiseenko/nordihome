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
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
@yield('head')

<!-- BEGIN: CSS Assets-->
    @vite('resources/css/admin.css')
    @vite('resources/js/admin.js')
    @stack('styles')
    @livewireStyles
<!-- END: CSS Assets-->
</head>
<!-- END: Head -->

<body class="@yield('body')">
@yield('content')
@include('flash::message')
<!-- @ include('layouts.partials.notification') -->

<!-- BEGIN: Vendor JS Assets-->
@stack('vendors')
<!-- END: Vendor JS Assets-->
<!-- BEGIN: Pages, layouts, components JS Assets-->
@stack('scripts')
<!-- END: Pages, layouts, components JS Assets-->
<script type="text/javascript">
    //Уведомления ajax из Компонентов
    document.addEventListener('livewire:init', () => {
        Livewire.on('window-notify', (event) => {
            if(event.icon === undefined) event.icon = 'danger';
            window.notification(event.title, event.message, event.icon);
        });
        Livewire.on('tom-select-sync', (elem) => {
            let _sel = document.getElementById(elem.id);
            let values = JSON.parse(elem.value);

            if (Array.isArray(values)) {
                values.forEach(function(value) {
                    _sel.tomselect.addItem(value);
                });
            } else {
                _sel.tomselect.addItem(elem.value);
            }
        });
    });
</script>
@livewireScripts

<!--script src="https://unpkg.com/lucide@latest"></script>
<script>
//    lucide.createIcons();
</script-->
</body>

</html>
