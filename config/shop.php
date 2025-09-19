<?php

return [
    /* COMMON */
    'theme' => env('SHOP_THEME', ''),
    'yookassa-id' => env('YOOKASSA_SHOP_ID', ''),
    'yookassa-key' => env('YOOKASSA_KEY', ''),
    'crm' => [
        'name-crm' => 'shop-L',
        'version' => '0.1',
    ],
    /* PAGINATION */
    'p-list' => 20,
    'p-card' => 9,
    'options-list' => [20, 40, 100, 200, 500],
    'options-card' => [3, 9, 15, 30, 90],


    /* IMAGE */
    //TODO Перенести в DB
    'report' => [
        'supply' => [
            'template' => '/template/accounting/_supply.xlsx',
        ],
        'arrival' => [
            'template' => '/template/accounting/_arrival.xlsx',
        ],
        'surplus' => [
            'template' => '/template/accounting/_surplus.xlsx',
        ],
        'movement' => [
            'template' => '/template/accounting/_movement.xlsx',
        ],
        'departure' => [
            'template' => '/template/accounting/_departure.xlsx',
        ],
        'inventory' => [
            'template' => '/template/accounting/_inventory.xlsx',
        ],
        'utd' => [
            'template' => '/template/accounting/_utd2024.xlsx',
        ],
        'base_path' => '',
        'trade12' => [
            'template' => '/template/_trade12.xlsx',
        ],


        //документы под каждый шаблон
        'invoice' => [
            'template' => '/template/_invoice.xlsx',
            'nordihome' => '/template/_invoice_nordihome.xlsx',
            'nbrussia' => '/template/_invoice_nbrussia.xlsx',
            ],
    ],

    'frontend' => [
        'logo-nav' => '/images/logo-nordi-home-2.svg',
        'brand-alt' => 'NORDI Home',
    ],
    'tinymce' => env('TINYMCE', ''),


];
