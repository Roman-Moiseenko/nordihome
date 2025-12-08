<?php

return [
    'parser' => [
        'sort' => 45,
        'icon' => 'store',
        'title' => 'Парсер',
        'can' => 'product',
        'vue' => true,
        'font_awesome' => 'fa-light fa-folder-magnifying-glass',
        'sub_menu' => [

            'category' => [
                'icon' => 'file-box',
                'title' => 'Категории Икеа',
                'route_name' => 'admin.parser.category.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-folder-tree',
            ],
              'product' => [
                  'icon' => 'package-open',
                  'title' => 'Товары Икеа',
                  'route_name' => 'admin.parser.product.index',
                  'vue' => true,
                  'font_awesome' => 'fa-light fa-box-open',
              ],


            'logs' => [
                'title' => 'История',
                'route_name' => 'admin.parser.log.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-book',
            ],
        ],
    ],
];
