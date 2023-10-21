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

    /* IMAGE */
    'image' => [
        'watermark' => '/images/watermark.png',
        'createThumbsOnSave' => true,
        'createThumbsOnRequest' => true,
        'thumbs' => [
            'thumb' => ['width' => 150, 'height' => 150,],
            'list' => ['width' => 200, 'height' => 200,],
            'card' => ['width' => 700, 'height' => 700,'watermark' => true],
            'original' => ['watermark' => true]
        ],
        'default' => [
            'Brand' => '/images/default-brand.png',
            'Catalog.image' => '/images/default-catalog.jpg',
            'Catalog.icon' => '/images/default-catalog.png',


        ],
        //другие параметры
    ],
];
