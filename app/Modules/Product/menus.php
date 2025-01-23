<?php

return [
    'shop' => [
        'sort' => 40,
        'icon' => 'store',
        'title' => 'Магазин',
        'can' => 'product',
        'vue' => true,
        'font_awesome' => 'fa-light fa-shop',
        'sub_menu' => [
            'product' => [
                'icon' => 'package-open',
                'title' => 'Все Товары',
                'route_name' => 'admin.product.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-box-open',
            ],
            'category' => [
                'icon' => 'file-box',
                'title' => 'Категории',
                'route_name' => 'admin.product.category.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-folder-tree',
            ],
            'modification' => [
                'icon' => 'file-cog',
                'title' => 'Модификации',
                'route_name' => 'admin.product.modification.index', // 'admin.product.tag.index'
                'vue' => true,
                'font_awesome' => 'fa-light fa-folder-gear',
            ],
           /* 'option' => [
                'icon' => 'package-plus',
                'title' => 'Опции',
                'route_name' => 'admin.home', // 'admin.product.tag.index'
            ],*/
            'equivalent' => [
                'icon' => 'package-check',
                'title' => 'Аналоги',
                'route_name' => 'admin.product.equivalent.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-balloons',
            ],
            'group' => [
                'icon' => 'boxes',
                'title' => 'Группы товаров',
                'route_name' => 'admin.product.group.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-boxes-stacked',
            ],
            'attribute' => [
                'icon' => 'blocks',
                'title' => 'Атрибуты',
                'route_name' => 'admin.product.attribute.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-pallet-boxes',
            ],
            'tags' => [
                'icon' => 'tags',
                'title' => 'Метки',
                'route_name' => 'admin.product.tag.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-tags',
            ],
            'brands' => [
                'icon' => 'pocket',
                'title' => 'Бренды',
                'route_name' => 'admin.product.brand.index',
                    'vue' => true,
                    'font_awesome' => 'fa-light fa-copyright',
            ],
            'series' => [
                'icon' => 'component',
                'title' => 'Серии',
                'route_name' => 'admin.product.series.index',
                'vue' => true,
                'font_awesome' => 'fa-regular fa-booth-curtain',
            ],
           /* 'size' => [
                'icon' => 'component',
                'title' => 'Размеры',
                'route_name' => 'admin.product.size.index',
                'vue' => true,
                'font_awesome' => 'fa-regular fa-maximize',
            ],*/
            'priority' => [
                'icon' => 'flag-triangle-right  ',
                'title' => 'Приоритет',
                'route_name' => 'admin.product.priority.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-flag-pennant',
            ],
            'parser' => [
                'icon' => 'package-search',
                'title' => 'Парсер * (временно)',
                'route_name' => 'admin.product.parser.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-folder-magnifying-glass',
            ],
        ],
    ],
];
