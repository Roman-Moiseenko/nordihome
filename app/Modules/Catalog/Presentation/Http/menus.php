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
                'route_name' => 'admin.catalog.product.index',
                'font_awesome' => 'fa-light fa-box-open',
            ],
            'category' => [
                'title' => 'Категории',
                'route_name' => 'admin.catalog.category.index',
                'font_awesome' => 'fa-light fa-folder-tree',
            ],
            'rooms' => [
                'title' => 'По комнатам',
                'route_name' => 'admin.catalog.room.index',
                'font_awesome' => 'fa-light fa-house-laptop',
            ],
            'modification' => [
                'title' => 'Модификации',
                'route_name' => 'admin.catalog.modification.index', // 'admin.catalog.tag.index'
                'font_awesome' => 'fa-light fa-folder-gear',
            ],
            'equivalent' => [
                'title' => 'Аналоги',
                'route_name' => 'admin.catalog.equivalent.index',
                'font_awesome' => 'fa-light fa-balloons',
            ],
            'group' => [
                'title' => 'Группы товаров',
                'route_name' => 'admin.catalog.group.index',
                'font_awesome' => 'fa-light fa-boxes-stacked',
            ],
            'attribute' => [
                'title' => 'Атрибуты',
                'route_name' => 'admin.catalog.attribute.index',
                'font_awesome' => 'fa-light fa-pallet-boxes',
            ],
            'tags' => [
                'title' => 'Метки',
                'route_name' => 'admin.catalog.tag.index',
                'font_awesome' => 'fa-light fa-tags',
            ],
            'brands' => [
                'title' => 'Бренды',
                'route_name' => 'admin.catalog.brand.index',
                'font_awesome' => 'fa-light fa-copyright',
            ],
            'series' => [
                'title' => 'Серии',
                'route_name' => 'admin.catalog.series.index',
                'font_awesome' => 'fa-regular fa-booth-curtain',
            ],
            'priority' => [
                'title' => 'Приоритет',
                'route_name' => 'admin.catalog.priority.index',
                'font_awesome' => 'fa-light fa-flag-pennant',
            ],
            'price_reduced' => [
                'title' => 'Цена снижена',
                'route_name' => 'admin.catalog.reduced.index',
                'font_awesome' => 'fa-light fa-money-check-dollar-pen',
            ],
            'only_on_order' => [
                'title' => 'Только под заказ',
                'route_name' => 'admin.catalog.on-order.index', //
                'font_awesome' => 'fa-light fa-truck-container',
            ],
        ],
    ],
];
