<?php

return [
    'shop' => [
        'sort' => 40,
        'title' => 'Магазин',
        'can' => 'product',
        'font_awesome' => 'fa-light fa-shop',
        'sub_menu' => [
            'product' => [
                'title' => 'Все Товары',
                'route_name' => 'admin.product.product.index',
                'font_awesome' => 'fa-light fa-box-open',
            ],
            'category' => [
                'title' => 'Категории',
                'route_name' => 'admin.product.category.index',
                'font_awesome' => 'fa-light fa-folder-tree',
            ],
            'rooms' => [
                'title' => 'По комнатам',
                'route_name' => 'admin.product.category.index',
                'font_awesome' => 'fa-light fa-house-laptop',
            ],
            'modification' => [
                'title' => 'Модификации',
                'route_name' => 'admin.product.modification.index', // 'admin.product.tag.index'
                'font_awesome' => 'fa-light fa-folder-gear',
            ],
            'equivalent' => [
                'title' => 'Аналоги',
                'route_name' => 'admin.product.equivalent.index',
                'font_awesome' => 'fa-light fa-balloons',
            ],
            'group' => [
                'title' => 'Группы товаров',
                'route_name' => 'admin.product.group.index',
                'font_awesome' => 'fa-light fa-boxes-stacked',
            ],
            'attribute' => [
                'title' => 'Атрибуты',
                'route_name' => 'admin.product.attribute.index',
                'font_awesome' => 'fa-light fa-pallet-boxes',
            ],
            'tags' => [
                'title' => 'Метки',
                'route_name' => 'admin.product.tag.index',
                'font_awesome' => 'fa-light fa-tags',
            ],
            'brands' => [
                'title' => 'Бренды',
                'route_name' => 'admin.product.brand.index',
                    'font_awesome' => 'fa-light fa-copyright',
            ],
            'series' => [
                'title' => 'Серии',
                'route_name' => 'admin.product.series.index',
                'font_awesome' => 'fa-regular fa-booth-curtain',
            ],
            'priority' => [
                'title' => 'Приоритет',
                'route_name' => 'admin.product.priority.index',
                'font_awesome' => 'fa-light fa-flag-pennant',
            ],
            'price_reduced' => [
                'title' => 'Цена снижена',
                'route_name' => 'admin.product.reduced.index',
                'font_awesome' => 'fa-light fa-money-check-dollar-pen',
            ],
            'only_on_order' => [
                'title' => 'Только под заказ',
                'route_name' => 'admin.product.on-order.index', //
                'font_awesome' => 'fa-light fa-truck-container',
            ],
            'parser' => [
                'title' => 'Парсер * (временно)',
                'route_name' => 'admin.product.parser.index',
                'font_awesome' => 'fa-light fa-folder-magnifying-glass',
            ],
        ],
    ],
];
