<?php

return [
    'orders' => [
        'sort' => 20,
        'icon' => 'coins',
        'title' => 'Продажи',
        'can' => ['order','payment', 'refund'],
        'vue' => true,
        'font_awesome' => 'fa-light fa-coin',
        'sub_menu' => [
            'order' => [
                'icon' => 'file-plus-2',
                'title' => 'Заказы',
                'route_name' => 'admin.order.index',
                'can' => 'order',
                'vue' => true,
                'font_awesome' => 'fa-light fa-cart-plus',
            ],
            'product' => [
                'icon' => 'package-open',
                'title' => 'Все Товары',
                'route_name' => 'admin.order.product.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-box-open',
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
