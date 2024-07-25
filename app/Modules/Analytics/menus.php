<?php

return [
    'analytics' => [
        'sort' => 110,
        'icon' => 'scroll-text',
        'title' => 'Логгеры',
        'can' => 'admin-panel',
        'sub_menu' => [
            'shop' => [
                'icon' => 'users',
                'title' => 'Сотрудников',
                'route_name' => 'admin.analytics.activity.index',
            ],
            'admin-panel' => [
                'icon' => 'timer-reset',
                'title' => 'По расписанию',
                'route_name' => 'admin.analytics.cron.index',
            ],

        ],
    ],
];
