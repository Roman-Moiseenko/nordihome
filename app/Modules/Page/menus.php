<?php

return [
    'page' => [
        'sort' => 90,
        'title' => 'Фронтенд',
        'can' => 'options',
        'vue' => true,
        'font_awesome' => 'fa-light fa-desktop',
        'sub_menu' => [
            'widgets' => [
                'title' => 'Виджеты',
                'route_name' => 'admin.page.widget.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-film',
            ],
            'pages' => [
                'title' => 'Страницы',
                'route_name' => 'admin.page.page.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-files',
            ],
         /*   'maps' => [
                'icon' => 'map-pinned',
                'title' => 'Карты',
                'route_name' => 'admin.home',
            ],*/
            'contacts' => [
                'title' => 'Контакты',
                'route_name' => 'admin.page.contact.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-address-book',
            ],
            'banners' => [
                'title' => 'Баннеры',
                'route_name' => 'admin.page.banner.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-files',
            ],

            'cache' => [
                'title' => 'Кеш страниц',
                'route_name' => 'admin.page.cache.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-database',
            ],
        ],
    ],
];
