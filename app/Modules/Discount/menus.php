<?php

return [
    'discount' => [
        'sort' => 50,
        'icon' => 'badge-percent',
        'title' => 'Скидки',
        'can' => 'discount',
        'sub_menu' => [
            'promotion' => [
                'icon' => 'percent',
                'title' => 'Акции',
                'route_name' => 'admin.discount.promotion.index',
            ],
            'coupon' => [
                'icon' => 'piggy-bank',
                'title' => 'Купоны скидочные',
                'route_name' => 'admin.home',
            ],
            'discount' => [
                'icon' => 'percent-diamond',
                'title' => 'Скидки',
                'route_name' => 'admin.discount.discount.index',
            ],
            'bonus' => [
                'icon' => 'badge-dollar-sign',
                'title' => 'Бонусные продажи',
                'route_name' => 'admin.discount.discount.index',
            ],
//
        ],
    ],
    'discount_divider' => [
        'sort' => 51,
    ],
];
