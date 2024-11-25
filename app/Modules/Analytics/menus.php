<?php

return [
    'analytics' => [
        'sort' => 110,
        'icon' => 'scroll-text',
        'title' => 'Логгеры',
        'can' => 'admin-panel',
        'font_awesome' => 'fa-light fa-scroll',
        'sub_menu' => [
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
    ],
];
