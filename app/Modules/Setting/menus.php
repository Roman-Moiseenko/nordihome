<?php

return [
    'settings' => [
        'sort' => 110,
        'icon' => 'settings',
        'title' => 'Настройки2',
        'can' => 'admin-panel',
        'sub_menu' => [
            'common' => [
                'icon' => 'store',
                'title' => 'Общие',
                'route_name' => 'admin.setting.common',
                'action' => true,
            ],
            'parser' => [
                'icon' => 'package-search',
                'title' => 'Парсер',
                'route_name' => 'admin.setting.parser',
                'action' => true,
            ],
            'coupon' => [
                'icon' => 'piggy-bank',
                'title' => 'Купоны',
                'route_name' => 'admin.setting.coupon',
                'action' => true,
            ],
            'web' => [
                'icon' => 'layout-panel-top',
                'title' => 'Сайт',
                'route_name' => 'admin.setting.web',
                'action' => true,
            ],
        ],
    ],
];
