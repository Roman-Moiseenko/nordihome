<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin menu items for Output module
|--------------------------------------------------------------------------
|
| Register menu items following the format below.
| Replace 'Output' with the plural form (e.g., 'users', 'pages').
|
| Each item requires:
|   - sort:        int (sorting order in sidebar)
|   - icon:        string (Lucide icon name, e.g. 'users', 'settings')
|   - title:       string (display text in sidebar)
|   - route_name:  string (named route, e.g. 'admin.output.index')
|   - can:         string (permission gate, e.g. 'staff', 'pages')
|   - vue:         bool (uses Vue/Inertia frontend)
|   - font_awesome: string (Font Awesome class, e.g. 'fa-light fa-users')
|
*/

return [
    'output' => [
        'sort' => 96,
        'icon' => 'settings',
        'title' => 'Выгрузки',
        'can' => 'admin-panel',
        'font_awesome' => 'fa-light fa-cloud-arrow-up',
        'sub_menu' => [
            'fids' => [
                'title' => 'Фиды',
                'route_name' => 'admin.output.feed.index',
                'font_awesome' => 'fa-light fa-rss',
            ],

        ],
    ],
];
