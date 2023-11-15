<?php

return [
    /* COMMON */
    'crm' => [
        'name-crm' => 'shop-L',
        'version' => '0.1',
    ],
    /* PAGINATION */
    'p-list' => 20,
    'p-card' => 9,
    'options-list' => [20, 40, 100, 200, 500],
    'options-card' => [3, 9, 15, 30, 90],

    //***  OPTIONS   **//

    /* IMAGE */
    'image' => [
        'watermark' => [
            'file' => '/images/watermark.png',
            'size' => 0.2, //от размера изображения
            'position' => 'bottom-right',
            'offset' => 20,
        ],
        'createThumbsOnSave' => true,
        'createThumbsOnRequest' => true,
        'thumbs' => [
            'thumb' => ['width' => 150, 'height' => 150,],
            'list' => ['width' => 200, 'height' => 200,],
            'card' => ['width' => 700, 'height' => 700,'watermark' => true],
            'original' => ['watermark' => true]
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

    /* SHOP => DB*/

    'shop' => [
        'preorder' => false,
        'only-offline' => false,
        'show-finished' => true,
        'delivery' => [
            'local' => true,
            'all' => true,
        ],
        'product' => [

        ],
    ],
    'frontend' => [
        'logo-nav' => '/images/logo-nordi-home-2.svg',
        'brand-alt' => 'NORDI Home',
    ],
];
