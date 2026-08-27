<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin menu items for Discount module
|--------------------------------------------------------------------------
|
| Register menu items following the format below.
| Replace 'Discount' with the plural form (e.g., 'users', 'pages').
|
| Each item requires:
|   - sort:        int (sorting order in sidebar)
|   - icon:        string (Lucide icon name, e.g. 'users', 'settings')
|   - title:       string (display text in sidebar)
|   - route_name:  string (named route, e.g. 'admin.discount.index')
|   - can:         string (permission gate, e.g. 'staff', 'pages')
|   - vue:         bool (uses Vue/Inertia frontend)
|   - font_awesome: string (Font Awesome class, e.g. 'fa-light fa-users')
|
*/

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
            /*     'coupon' => [
                     'icon' => 'piggy-bank',
                     'title' => 'Купоны скидочные',
                     'route_name' => 'admin.home',
                     'vue' => true,
                     'font_awesome' => 'fa-light fa-piggy-bank',
                 ],*/
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
