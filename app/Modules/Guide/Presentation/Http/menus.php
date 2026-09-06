<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin menu items for Guide module
|--------------------------------------------------------------------------
|
| Register menu items following the format below.
| Replace 'Guide' with the plural form (e.g., 'users', 'pages').
|
| Each item requires:
|   - sort:        int (sorting order in sidebar)
|   - icon:        string (Lucide icon name, e.g. 'users', 'settings')
|   - title:       string (display text in sidebar)
|   - route_name:  string (named route, e.g. 'admin.guide.index')
|   - can:         string (permission gate, e.g. 'staff', 'pages')
|   - vue:         bool (uses Vue/Inertia frontend)
|   - font_awesome: string (Font Awesome class, e.g. 'fa-light fa-users')
|
*/

return [
    'guide' => [
        'sort' => 105,
        'icon' => 'database',
        'title' => 'Справочники',
        'can' => '',
        'vue' => true,
        'font_awesome' => 'fa-light fa-book',
        'route_name' => 'admin.guide.index',

    ],
];
