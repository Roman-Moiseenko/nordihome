<?php

return [
    'shop' => [
        'sort' => 40,
        'icon' => 'store',
        'title' => 'Магазин',
        'can' => 'product',
        'sub_menu' => [
            'product' => [
                'icon' => 'package-open',
                'title' => 'Все Товары',
                'route_name' => 'admin.product.index',
            ],
            'category' => [
                'icon' => 'file-box',
                'title' => 'Категории',
                'route_name' => 'admin.product.category.index',
            ],
            'modification' => [
                'icon' => 'file-cog',
                'title' => 'Модификации',
                'route_name' => 'admin.product.modification.index', // 'admin.product.tag.index'
            ],
            'option' => [
                'icon' => 'package-plus',
                'title' => 'Опции',
                'route_name' => 'admin.home', // 'admin.product.tag.index'
            ],
            'equivalent' => [
                'icon' => 'package-check',
                'title' => 'Аналоги',
                'route_name' => 'admin.product.equivalent.index',
            ],
            'group' => [
                'icon' => 'boxes',
                'title' => 'Группы товаров',
                'route_name' => 'admin.product.group.index',
            ],
            'attribute' => [
                'icon' => 'blocks',
                'title' => 'Атрибуты',
                'route_name' => 'admin.product.attribute.index',
            ],
            'tags' => [
                'icon' => 'tags',
                'title' => 'Метки',
                'route_name' => 'admin.product.tag.index',
            ],
            'brands' => [
                'icon' => 'pocket',
                'title' => 'Бренды',
                'route_name' => 'admin.product.brand.index',
            ],
            'series' => [
                'icon' => 'component',
                'title' => 'Серии',
                'route_name' => 'admin.product.series.index',
            ],
            'priority' => [
                'icon' => 'flag-triangle-right  ',
                'title' => 'Приоритет',
                'route_name' => 'admin.product.priority.index',
            ],
            'parser' => [
                'icon' => 'package-search',
                'title' => 'Парсер',
                'route_name' => 'admin.product.parser.index',
            ],
        ],
    ],
];
