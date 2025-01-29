<?php

return [
    'page' => [
        'sort' => 90,
        'icon' => 'monitor',
        'title' => 'Фронтенд',
        'can' => 'options',
        'vue' => true,
        'font_awesome' => 'fa-light fa-desktop',
        'sub_menu' => [
            'widgets' => [
                'icon' => 'film',
                'title' => 'Виджеты',
                'route_name' => 'admin.page.widget.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-film',
            ],
            'pages' => [
                'icon' => 'files',
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
                'icon' => 'contact',
                'title' => 'Контакты',
                'route_name' => 'admin.page.contact.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-address-book',
            ],
            'banners' => [
                'icon' => 'book-image', //book-image  gallery-horizontal-end
                'title' => 'Баннеры',
                'route_name' => 'admin.page.banner.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-files',
            ],

        ],
    ],
];
