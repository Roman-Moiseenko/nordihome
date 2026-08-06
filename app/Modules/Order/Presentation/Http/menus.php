<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin menu items for Order module
|--------------------------------------------------------------------------
|
| Register menu items following the format below.
| Replace 'Order' with the plural form (e.g., 'users', 'pages').
|
| Each item requires:
|   - sort:        int (sorting order in sidebar)
|   - icon:        string (Lucide icon name, e.g. 'users', 'settings')
|   - title:       string (display text in sidebar)
|   - route_name:  string (named route, e.g. 'admin.order.index')
|   - can:         string (permission gate, e.g. 'staff', 'pages')
|   - vue:         bool (uses Vue/Inertia frontend)
|   - font_awesome: string (Font Awesome class, e.g. 'fa-light fa-users')
|
*/

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
                'vue' => true,
                'font_awesome' => 'fa-light fa-credit-card',
            ],
            'refund' => [
                'icon' => 'refresh-ccw',
                'title' => 'Возвраты',
                'route_name' => 'admin.order.refund.index',
                'can' => 'refund',
                'vue' => true,
                'font_awesome' => 'fa-light fa-rotate-right',
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
