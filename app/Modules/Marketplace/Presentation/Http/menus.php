<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin menu items for Marketplace module
|--------------------------------------------------------------------------
|
| Register menu items following the format below.
| Replace 'Marketplace' with the plural form (e.g., 'users', 'pages').
|
| Each item requires:
|   - sort:        int (sorting order in sidebar)
|   - icon:        string (Lucide icon name, e.g. 'users', 'settings')
|   - title:       string (display text in sidebar)
|   - route_name:  string (named route, e.g. 'admin.marketplace.index')
|   - can:         string (permission gate, e.g. 'staff', 'pages')
|   - vue:         bool (uses Vue/Inertia frontend)
|   - font_awesome: string (Font Awesome class, e.g. 'fa-light fa-users')
|
*/

return [
    'marketplaces' => [
        'sort'         => 11,
        'title'        => 'Маркетплейсы',
        'can'          => 'marketplace',
        'vue'          => true,
        'font_awesome' => 'fa-light fa-rectangle-pro',
        'sub_menu'     => [
            'product' => [
                'title' => 'Авито',
                'route_name' => 'admin.marketplace.avito.index',
                'font_awesome' => 'fa-light fa-circle-a',
            ],
        ],
    ],
];
