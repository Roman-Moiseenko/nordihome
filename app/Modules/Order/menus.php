<?php

return [
    'orders' => [
        'sort' => 20,
        'icon' => 'coins',
        'title' => 'Продажи',
        'can' => ['order','payment', 'refund'],
        'sub_menu' => [
            'order' => [
                'icon' => 'file-plus-2',
                'title' => 'Заказы',
                'route_name' => 'admin.order.index',
                'can' => 'order',
            ],
            'payment' => [
                'icon' => 'credit-card',
                'title' => 'Платежи',
                'route_name' => 'admin.order.payment.index',
                'can' => 'payment',
            ],
            'refund' => [
                'icon' => 'refresh-ccw',
                'title' => 'Возвраты',
                'route_name' => 'admin.order.refund.index',
                'can' => 'refund',
            ],
            'reserve' => [
                'icon' => 'baggage-claim',
                'title' => 'Резерв',
                'route_name' => 'admin.order.reserve.index',
                'can' => 'order',
            ],
        ],
    ],
];
