<?php

return [
    'delivery' => [
        'sort' => 30,
        'icon' => 'plane',
        'title' => 'Доставка',
        'can' => ['delivery'],
        'sub_menu' => [
            'truck' => [
                'icon' => 'truck',
                'title' => 'Транспорт',
                'route_name' => 'admin.delivery.truck.index',
            ],
            'storage' => [
                'icon' => 'warehouse',
                'title' => 'Выдача со склада',
                'route_name' => 'admin.delivery.storage',
                'action' => true,
            ],
            'local' => [
                'icon' => 'map-pin',
                'title' => 'Доставка по региону',
                'route_name' => 'admin.delivery.local',
                'action' => true,
            ],
            'region' => [
                'icon' => 'map',
                'title' => 'Доставка по РФ',
                'route_name' => 'admin.delivery.region',
                'action' => true,
            ],
            'calendar' => [
                'icon' => 'calendar-days',
                'title' => 'Календарь доставок',
                'route_name' => 'admin.delivery.calendar.index',
                //'action' => true,
            ],
            'schedule' => [
                'icon' => 'calendar-check',
                'title' => 'График доставок',
                'route_name' => 'admin.delivery.calendar.schedule',
                'action' => true,
            ],
        ],

    ],
    'delivery_divider' => [
        'sort' => 31,
    ],
];
