<?php

return [
    'discount' => [
        'sort' => 50,
        'icon' => 'badge-percent',
        'title' => 'Скидки',
        'can' => 'discount',
        'vue' => true,
        'font_awesome' => 'fa-light fa-droplet-percent',
        'sub_menu' => [
            'promotion' => [
                'icon' => 'percent',
                'title' => 'Акции',
                'route_name' => 'admin.discount.promotion.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-percent'
            ],
            'coupon' => [
                'icon' => 'piggy-bank',
                'title' => 'Купоны скидочные',
                'route_name' => 'admin.home',
                'vue' => true,
                'font_awesome' => 'fa-light fa-piggy-bank',
            ],
            'discount' => [
                'icon' => 'percent-diamond',
                'title' => 'Скидки',
                'route_name' => 'admin.discount.discount.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-badge-percent'
            ],
          /*  'bonus' => [
                'icon' => 'badge-dollar-sign',
                'title' => 'Бонусные продажи',
                'route_name' => 'admin.discount.discount.index',
            ],*/
//
        ],
    ],
    'discount_divider' => [
        'sort' => 51,
    ],
];
