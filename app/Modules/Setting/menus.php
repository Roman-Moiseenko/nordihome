<?php

return [
    'settings' => [
        'sort' => 110,
        'icon' => 'settings',
        'title' => 'Настройки',
        'can' => 'admin-panel',
        'font_awesome' => 'fa-light fa-gear',
        'sub_menu' => [
            'common' => [
                'icon' => 'store',
                'title' => 'Общие',
                'route_name' => 'admin.setting.common',
                'action' => true,
                'vue' => true,
                'font_awesome' => 'fa-light fa-shop',
            ],
            'parser' => [
                'icon' => 'package-search',
                'title' => 'Парсер',
                'route_name' => 'admin.setting.parser',
                'action' => true,
                'vue' => true,
                'font_awesome' => 'fa-light fa-print-magnifying-glass',
            ],
            'coupon' => [
                'icon' => 'piggy-bank',
                'title' => 'Купоны',
                'route_name' => 'admin.setting.coupon',
                'action' => true,
                'vue' => true,
                'font_awesome' => 'fa-light fa-piggy-bank',
            ],
            'notification' => [
                'icon' => 'layout-panel-top',
                'title' => 'Уведомления',
                'route_name' => 'admin.setting.notification',
                'action' => true,
                'vue' => true,
                'font_awesome' => 'fa-light fa-bell',
            ],
            'web' => [
                'icon' => 'layout-panel-top',
                'title' => 'Сайт',
                'route_name' => 'admin.setting.web',
                'action' => true,
                'vue' => true,
                'font_awesome' => 'fa-light fa-table-layout',
            ],
            'mail' => [
                'title' => 'Почта',
                'action' => true,
                'vue' => true,
                'route_name' => 'admin.setting.mail',
                'font_awesome' => 'fa-light fa-envelope',
            ],
        ],
    ],
];
