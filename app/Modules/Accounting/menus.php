<?php

return [
    'accounting' => [
        'sort' => 60,
        'icon' => 'database',
        'title' => 'Товарный учет',
        'can' => 'accounting',
        'sub_menu' => [
            'arrival' => [
                'icon' => 'folder-input',
                'title' => 'Поступление',
                'route_name' => 'admin.accounting.arrival.index',
            ],
            'movement' => [
                'icon' => 'folder-sync',
                'title' => 'Перемещение товара',
                'route_name' => 'admin.accounting.movement.index',
            ],
            'departure' => [
                'icon' => 'folder-output',
                'title' => 'Списание товара',
                'route_name' => 'admin.accounting.departure.index',
            ],
            'supply' => [
                'icon' => 'folder-pen',
                'title' => 'Заказы поставщикам',
                'route_name' => 'admin.accounting.supply.index',
            ],
            'pricing' => [
                'icon' => 'badge-russian-ruble',
                'title' => 'Ценообразование',
                'route_name' => 'admin.accounting.pricing.index',
            ],
            'distributors' => [
                'icon' => 'building',
                'title' => 'Поставщики',
                'route_name' => 'admin.accounting.distributor.index',
            ],
            'storages' => [
                'icon' => 'warehouse',
                'title' => 'Хранилища',
                'route_name' => 'admin.accounting.storage.index',
            ],
            'currency' => [
                'icon' => 'candlestick-chart',
                'title' => 'Курс валют',
                'route_name' => 'admin.accounting.currency.index',
            ],
            'organization' => [
                'icon' => 'landmark',
                'title' => 'Организации',
                'route_name' => 'admin.accounting.organization.index',
                'can' => '',
            ],
        ],
    ],
    'accounting_divider' => [
        'sort' => 61,
    ],
];
