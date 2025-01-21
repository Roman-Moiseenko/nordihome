<?php

return [
    'shop' => [
        'sort' => 45,
        'icon' => 'store',
        'title' => 'Парсер',
        'can' => 'product',
        'vue' => true,
        'font_awesome' => 'fa-light fa-shop',
        'sub_menu' => [
          /*  'product' => [
                'icon' => 'package-open',
                'title' => 'Все Товары',
                'route_name' => 'admin.parser.product.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-box-open',
            ],*/
            'category' => [
                'icon' => 'file-box',
                'title' => 'Категории',
                'route_name' => 'admin.parser.category.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-folder-tree',
            ],

        ],
    ],
];
