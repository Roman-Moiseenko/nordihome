<?php

return [
    'accounting' => [
        'sort' => 60,
        'icon' => 'database',
        'title' => 'Товарный учет',
        'can' => 'accounting',
        'sub_menu' => [
            'supply' => [
                'icon' => 'folder-pen',
                'title' => 'Заказы поставщикам',
                'route_name' => 'admin.accounting.supply.index',
                'vue' => true,
                'font_awesome' => 'fa-sharp fa-light fa-money-check-pen',
            ],
            'arrival' => [
                'icon' => 'folder-input',
                'title' => 'Приходные накладные',
                'route_name' => 'admin.accounting.arrival.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-folder-arrow-down',
            ],
            'payment-order' => [
                'icon' => 'folder-pen',
                'title' => 'Платежные поручения',
                'route_name' => 'admin.accounting.payment.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-file-invoice',
            ],
            'refund' => [
                'icon' => 'folder-input',
                'title' => 'Возвраты поставщикам',
                'route_name' => 'admin.accounting.refund.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-folder-arrow-up',
            ],
            'movement' => [
                'icon' => 'folder-sync',
                'title' => 'Перемещение товара',
                'route_name' => 'admin.accounting.movement.index',
                'vue' => true,
                'font_awesome' => 'fa-sharp fa-light fa-arrows-rotate',
            ],
            'departure' => [
                'icon' => 'folder-output',
                'title' => 'Списание товара',
                'route_name' => 'admin.accounting.departure.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-folder-xmark',
            ],
            'pricing' => [
                'icon' => 'badge-russian-ruble',
                'title' => 'Ценообразование',
                'route_name' => 'admin.accounting.pricing.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-circle-dollar',
            ],
            'inventory' => [
                'icon' => 'badge-russian-ruble',
                'title' => 'Инвентаризация',
                'route_name' => 'admin.accounting.inventory.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-scanner-keyboard',
            ],

            'distributors' => [
                'icon' => 'factory',
                'title' => 'Поставщики',
                'route_name' => 'admin.accounting.distributor.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-industry-windows',
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
                'vue' => true,
                'font_awesome' => 'fa-light fa-chart-candlestick',
            ],
            'trader' => [
                'icon' => 'building-2',
                'title' => 'Продавцы',
                'route_name' => 'admin.accounting.trader.index',
                'can' => '',
            ],
            'organization' => [
                'icon' => 'landmark',
                'title' => 'Организации',
                'route_name' => 'admin.accounting.organization.index',
                'can' => '',
                'vue' => true,
                'font_awesome' => 'fa-light fa-building-columns',
            ],
        ],
    ],
    'accounting_divider' => [
        'sort' => 61,
    ],
];
