<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin menu items for Analytics module
|--------------------------------------------------------------------------
|
| Register menu items following the format below.
| Replace 'Analytics' with the plural form (e.g., 'users', 'pages').
|
| Each item requires:
|   - sort:        int (sorting order in sidebar)
|   - icon:        string (Lucide icon name, e.g. 'users', 'settings')
|   - title:       string (display text in sidebar)
|   - route_name:  string (named route, e.g. 'admin.analytics.index')
|   - can:         string (permission gate, e.g. 'staff', 'pages')
|   - vue:         bool (uses Vue/Inertia frontend)
|   - font_awesome: string (Font Awesome class, e.g. 'fa-light fa-users')
|
*/

return [
    'analytics' => [
        'sort' => 110,
        'icon' => 'scroll-text',
        'title' => 'Логгеры',
        'can' => 'admin-panel',
        'font_awesome' => 'fa-light fa-scroll',
        /*    'sub_menu' => [
                'shop' => [
                    'icon' => 'users',
                    'title' => 'Сотрудников',
                    'route_name' => 'admin.analytics.activity.index',
                    'vue' => true,
                    'font_awesome' => 'fa-light fa-users',
                ],
                'admin-panel' => [
                    'icon' => 'timer-reset',
                    'title' => 'По расписанию',
                    'route_name' => 'admin.analytics.cron.index',
                    'vue' => true,
                    'font_awesome' => 'fa-light fa-timer',
                ],

            ],
            */
    ],
];
