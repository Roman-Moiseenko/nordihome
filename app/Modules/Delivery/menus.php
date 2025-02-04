<?php

return [
    'delivery' => [
        'sort' => 30,
        'icon' => 'plane',
        'title' => 'Доставка',
        'can' => ['delivery'],
        'font_awesome' => 'fa-light fa-truck-ramp-box',
        'vue' => true,
        'sub_menu' => [
            'assembly' => [
                'title' => 'На сборку',
                'route_name' => 'admin.delivery.to-loader',
                'action' => true,
                'vue' => true,
                'font_awesome' => 'fa-light fa-cart-flatbed-boxes',
            ],
            'delivery' => [
                'title' => 'На доставку',
                'route_name' => 'admin.delivery.to-delivery',
                'action' => true,
                'vue' => true,
                'font_awesome' => 'fa-light fa-person-dolly',
            ],
            'all' => [
                'title' => 'Все',
                'route_name' => 'admin.delivery.all',
                'action' => true,
                'vue' => true,
                'font_awesome' => 'fa-light fa-truck-ramp-box',
            ],
            'truck' => [
                'icon' => 'truck',
                'title' => 'Транспорт',
                'route_name' => 'admin.delivery.truck.index',
                'font_awesome' => 'fa-light fa-truck',
            ],
         /*   'storage' => [
                'icon' => 'warehouse',
                'title' => '*Выдача со склада',
                'route_name' => 'admin.delivery.storage',
                'action' => true,
            ],
            'local' => [
                'icon' => 'map-pin',
                'title' => '*Доставка по региону',
                'route_name' => 'admin.delivery.local',
                'action' => true,
            ],
            'region' => [
                'icon' => 'map',
                'title' => '*Доставка по РФ',
                'route_name' => 'admin.delivery.region',
                'action' => true,
            ],
            'calendar' => [
                'icon' => 'calendar-days',
                'title' => '*Календарь доставок',
                'route_name' => 'admin.delivery.calendar.index',
                //'action' => true,
            ],*/
            /*
            'schedule' => [
                'icon' => 'calendar-check',
                'title' => 'График доставок',
                'route_name' => 'admin.delivery.calendar.schedule',
                'action' => true,
            ],
            */
        ],

    ],
    'delivery_divider' => [
        'sort' => 31,
    ],
];
