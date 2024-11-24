<?php

return [
    'clients' => [
        'sort' => 10,
        'icon' => 'users',
        'title' => 'Клиенты',
        'can' => 'user',
        'font_awesome' => 'fa-light fa-user',
        'sub_menu' => [
            'users' => [
                'icon' => 'user-search',
                'title' => 'Список',
                'route_name' => 'admin.user.index',
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
    ],
    'clients_divider' => [
        'sort' => 11,
    ],
];
