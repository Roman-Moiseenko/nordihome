<?php

return [
    'page' => [
        'sort' => 90,
        'icon' => 'monitor',
        'title' => 'Фронтенд',
        'can' => 'options',
        'sub_menu' => [
            'widgets' => [
                'icon' => 'film',
                'title' => 'Виджеты',
                'route_name' => 'admin.page.widget.index',
            ],
            'pages' => [
                'icon' => 'files',
                'title' => 'Страницы',
                'route_name' => 'admin.page.page.index',
            ],
            'maps' => [
                'icon' => 'map-pinned',
                'title' => 'Карты',
                'route_name' => 'admin.home',
            ],
            'contacts' => [
                'icon' => 'contact',
                'title' => 'Контакты',
                'route_name' => 'admin.page.contact.index',
            ],
            'banners' => [
                'icon' => 'book-image', //book-image  gallery-horizontal-end
                'title' => 'Баннеры',
                'route_name' => 'admin.home',
            ],

        ],
    ],
];
