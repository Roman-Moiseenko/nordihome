<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin menu items for Parser module
|--------------------------------------------------------------------------
|
| Register menu items following the format below.
| Replace 'Parser' with the plural form (e.g., 'users', 'pages').
|
| Each item requires:
|   - sort:        int (sorting order in sidebar)
|   - icon:        string (Lucide icon name, e.g. 'users', 'settings')
|   - title:       string (display text in sidebar)
|   - route_name:  string (named route, e.g. 'admin.parser.index')
|   - can:         string (permission gate, e.g. 'staff', 'pages')
|   - vue:         bool (uses Vue/Inertia frontend)
|   - font_awesome: string (Font Awesome class, e.g. 'fa-light fa-users')
|
*/

return [
    'parser' => [
        'sort' => 45,
        'title' => 'Парсер',
        'can' => 'product',
        'font_awesome' => 'fa-light fa-folder-magnifying-glass',
        'sub_menu' => [
            'category' => [
                'title' => 'Категории Икеа',
                'route_name' => 'admin.parser.category.index',
                'font_awesome' => 'fa-light fa-folder-tree',
            ],
            'product' => [
                'title' => 'Товары Икеа',
                'route_name' => 'admin.parser.product.index',
                'font_awesome' => 'fa-light fa-box-open',
            ],

            'logs' => [
                'title' => 'История',
                'route_name' => 'admin.parser.log.index',
                'font_awesome' => 'fa-light fa-book',
            ],
        ],
    ],
];
