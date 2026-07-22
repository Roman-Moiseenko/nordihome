<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin menu items for Notification module
|--------------------------------------------------------------------------
|
| Register menu items following the format below.
| Replace 'Notification' with the plural form (e.g., 'users', 'pages').
|
| Each item requires:
|   - sort:        int (sorting order in sidebar)
|   - icon:        string (Lucide icon name, e.g. 'users', 'settings')
|   - title:       string (display text in sidebar)
|   - route_name:  string (named route, e.g. 'admin.notification.index')
|   - can:         string (permission gate, e.g. 'staff', 'pages')
|   - vue:         bool (uses Vue/Inertia frontend)
|   - font_awesome: string (Font Awesome class, e.g. 'fa-light fa-users')
|
*/

return [
    'notifications' => [
        'sort' => 75,
        'icon' => 'Bell',
        'title' => 'Уведомления',
        'route_name' => 'admin.notification.notification.index',
        'can' => '',
        'vue' => true,
        'font_awesome' => 'fa-light fa-bell'
    ],
];
