<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin menu items for Accounting module
|--------------------------------------------------------------------------
|
| Register menu items following the format below.
| Replace 'Accounting' with the plural form (e.g., 'users', 'pages').
|
| Each item requires:
|   - sort:        int (sorting order in sidebar)
|   - icon:        string (Lucide icon name, e.g. 'users', 'settings')
|   - title:       string (display text in sidebar)
|   - route_name:  string (named route, e.g. 'admin.accounting.index')
|   - can:         string (permission gate, e.g. 'staff', 'pages')
|   - vue:         bool (uses Vue/Inertia frontend)
|   - font_awesome: string (Font Awesome class, e.g. 'fa-light fa-users')
|
*/

return [
    'accounting' => [
        'sort' => 60,
        'icon' => 'database',
        'title' => 'Товарный учет',
        'can' => 'accounting',
        'vue' => true,
        'font_awesome' => 'fa-light fa-abacus',
        'sub_menu' => [
/*            'supply' => [
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
*/
            'pricing' => [
                'icon' => 'badge-russian-ruble',
                'title' => 'Ценообразование',
                'route_name' => 'admin.accounting.pricing.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-circle-dollar',
            ],
/*
            'inventory' => [
                'icon' => 'badge-russian-ruble',
                'title' => 'Инвентаризация',
                'route_name' => 'admin.accounting.inventory.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-scanner-keyboard',
            ],
            'departure' => [
                'icon' => 'folder-output',
                'title' => 'Списание товара',
                'route_name' => 'admin.accounting.departure.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-folder-xmark',
            ],
            'surplus' => [
                'icon' => 'folder-output',
                'title' => 'Оприходование',
                'route_name' => 'admin.accounting.surplus.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-folder-plus',
            ],
            */
            'distributors' => [
                'icon' => 'factory',
                'title' => 'Поставщики',
                'route_name' => 'admin.accounting.distributor.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-industry-windows',
            ],
     /*       'storages' => [
                'icon' => 'warehouse',
                'title' => 'Хранилища',
                'route_name' => 'admin.accounting.storage.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-warehouse',
            ],
            */
            'stock' => [
                'icon' => 'boxes',
                'title' => 'Остатки товаров',
                'route_name' => 'admin.accounting.stock.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-boxes-stacked',
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
                'vue' => true,
                'font_awesome' => 'fa-light fa-building',
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
