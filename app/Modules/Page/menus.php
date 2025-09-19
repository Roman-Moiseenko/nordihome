<?php

return [
    'page' => [
        'sort' => 90,
        'title' => 'Фронтенд',
        'can' => 'options',
        'vue' => true,
        'font_awesome' => 'fa-light fa-desktop',
        'sub_menu' => [
            'pages' => [
                'title' => 'Страницы',
                'route_name' => 'admin.page.page.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-files',
            ],
            'products' => [
                'title' => 'Виджеты товаров',
                'route_name' => 'admin.page.widget.product.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-film',
            ],
         /*   'maps' => [
                'icon' => 'map-pinned',
                'title' => 'Карты',
                'route_name' => 'admin.home',
            ],*/

            'banners' => [
                'title' => 'Баннеры (Виджет)',
                'route_name' => 'admin.page.widget.banner.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-files',
            ],
            'promotions' => [
                'title' => 'Виджеты Акций',
                'route_name' => 'admin.page.widget.promotion.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-percent',
            ],
            'texts' => [
                'title' => 'Текстовые виджеты',
                'route_name' => 'admin.page.widget.text.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-text-size',
            ],

            'contacts' => [
                'title' => 'Контакты',
                'route_name' => 'admin.page.contact.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-address-book',
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
