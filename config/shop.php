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
    //TODO Удалить
 /*   'image' => [
        'watermark' => [
            'file' => '/images/watermark.png',
            'size' => 0.2, //от размера изображения
            'position' => 'bottom-right',
            'offset' => 20,
        ],
        'createThumbsOnSave' => true,
        'createThumbsOnRequest' => true,
        'thumbs' => [
            'mini' => ['width' => 80, 'height' => 80,],
            'thumb' => ['width' => 150, 'height' => 150,],
            'list' => ['width' => 200, 'height' => 200,],
            //catalog - для списка товаров и категорий
            'catalog' => ['width' => 320, 'height' => 320,],
            'catalog-watermark' => ['width' => 320, 'height' => 320, 'watermark' => true],
            //Для карточки товара
            'card' => ['width' => 700, 'height' => 700,'watermark' => true],
            'slide' => ['width' => 500, 'height' => 500],
            //'card-no-watermark' => ['width' => 700, 'height' => 700],
            'promotion' => ['width' => 450, 'height' => 550,],
            'promotion-mini' => ['width' => 400, 'height' => 250,],
            'original' => ['watermark' => true],
            'banner' => ['width' => 1616, 'height' => 736, 'fit' => true],
        ],
        'path' => [
            'uploads' => '/uploads',
            'cache' => '/cache',
        ],
        'path-uploads' => '/uploads', //del
        'path-cache' => '/cache', //del
        'default' => [
            'Brand' => '/images/default-brand.png',
            'Catalog.image' => '/images/default-catalog.jpg',
            'Catalog.icon' => '/images/default-catalog.png',

        ],//другие параметры
    ],
    */
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
