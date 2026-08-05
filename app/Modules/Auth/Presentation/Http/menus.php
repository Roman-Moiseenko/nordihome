<?php

declare(strict_types=1);

return [
    'staff' => [
        'sort'         => 1,
        'icon'         => 'contact',
        'title'        => 'Сотрудники',
        'route_name'   => 'admin.staff.index',
        'can'          => 'staff',
        'vue'          => true,
        'font_awesome' => 'fa-light fa-address-book',
    ],
    'role' => [
        'sort'         => 2,
        'icon'         => 'shield-check',
        'title'        => 'Роли',
        'route_name'   => 'admin.role.index',
        'can'          => 'staff',
        'vue'          => true,
        'font_awesome' => 'fa-light fa-shield-check',
    ],

    'clients' => [
        'sort' => 10,
        'icon' => 'users',
        'title' => 'Клиенты',
        'route_name' => 'admin.client.index',
        'can' => 'user',
        'font_awesome' => 'fa-light fa-user',
            /*
        'sub_menu' => [
            'users' => [
                'icon' => 'user-search',
                'title' => 'Список',

                'vue' => true,
                'font_awesome' => 'fa-light fa-users',
            ],
            'subscriptions' => [
                'icon' => 'bell-ring',
                'title' => 'Подписки',
                'route_name' => 'admin.user.subscription.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-bell',
            ],
            'cart' => [
                'icon' => 'shopping-cart',
                'title' => 'Корзина',
                'route_name' => 'admin.user.cart.index',
                'can' => 'order',
                'vue' => true,
                'font_awesome' => 'fa-light fa-cart-shopping',
            ],
            'wish' => [
                'icon' => 'heart',
                'title' => 'Избранное',
                'route_name' => 'admin.user.wish.index',
                'can' => 'order',
                'vue' => true,
                'font_awesome' => 'fa-light fa-heart',
            ],
        ],
        */
    ],
    'clients_divider' => [
        'sort' => 11,
    ],
];
