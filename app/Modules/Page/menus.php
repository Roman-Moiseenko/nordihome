<?php

return [
    'page' => [
        'sort' => 90,
        'title' => 'Фронтенд',
        'can' => 'options',
        'vue' => true,
        'font_awesome' => 'fa-light fa-desktop',
        'sub_menu' => [
            /*'news' => [
                'title' => 'Новости',
                'route_name' => 'admin.page.news.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-files',
            ],*/
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
            'text' => [
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
            'post-categories' => [
                'title' => 'Записи',
                'route_name' => 'admin.page.post-category.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-folder-tree',
            ],
            'menus-list' => [
                'title' => 'Меню',
                'route_name' => 'admin.page.menu.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-memo',
            ],
            'gallery' => [
                'title' => 'Галерея',
                'route_name' => 'admin.page.gallery.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-images',
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
