<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin menu items for Page module
|--------------------------------------------------------------------------
|
| Register menu items following the format below.
| Replace 'Page' with the plural form (e.g., 'users', 'pages').
|
| Each item requires:
|   - sort:        int (sorting order in sidebar)
|   - icon:        string (Lucide icon name, e.g. 'users', 'settings')
|   - title:       string (display text in sidebar)
|   - route_name:  string (named route, e.g. 'admin.content.index')
|   - can:         string (permission gate, e.g. 'staff', 'pages')
|   - vue:         bool (uses Vue/Inertia frontend)
|   - font_awesome: string (Font Awesome class, e.g. 'fa-light fa-users')
|
*/

return [
    'content' => [
        'sort' => 90,
        'title' => 'Фронтенд',
        'can' => 'options',
        'vue' => true,
        'font_awesome' => 'fa-light fa-desktop',
        'sub_menu' => [
            /*'news' => [
                'title' => 'Новости',
                'route_name' => 'admin.content.news.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-files',
            ],*/
            'pages' => [
                'title' => 'Страницы',
                'route_name' => 'admin.content.page.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-files',
            ],
            'products' => [
                'title' => 'Виджеты товаров',
                'route_name' => 'admin.content.widget.product.index',
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
                'route_name' => 'admin.content.widget.banner.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-files',
            ],
            'promotions' => [
                'title' => 'Виджеты Акций',
                'route_name' => 'admin.content.widget.promotion.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-percent',
            ],
            'text' => [
                'title' => 'Текстовые виджеты',
                'route_name' => 'admin.content.widget.text.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-text-size',
            ],
            'posts' => [
                'title' => 'Виджеты записей',
                'route_name' => 'admin.content.widget.post.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-square-list',
            ],
            'contacts' => [
                'title' => 'Контакты',
                'route_name' => 'admin.content.contact.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-address-book',
            ],

            'post-categories' => [
                'title' => 'Записи',
                'route_name' => 'admin.content.post-category.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-folder-tree',
            ],
            'menus-list' => [
                'title' => 'Меню',
                'route_name' => 'admin.content.menu.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-memo',
            ],
            'gallery' => [
                'title' => 'Галерея',
                'route_name' => 'admin.content.gallery.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-images',
            ],
            'forms' => [
                'title' => 'Обратная связь',
                'route_name' => 'admin.content.widget.form.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-pen-field',
            ],
            'seo' => [
                'title' => 'SEO мета',
                'route_name' => 'admin.content.seo-meta.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-fill',
            ],
            'cache' => [
                'title' => 'Кеш страниц',
                'route_name' => 'admin.content.cache.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-database',
            ],
        ],
    ],
];
