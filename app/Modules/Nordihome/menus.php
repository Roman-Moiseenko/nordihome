<?php

if (config('shop.theme') != 'nordihome') return [];
return [
    'nordihome' => [
        'sort' => 200,
        'icon' => 'database',
        'title' => 'Nordihome *',
        'can' => '',
        'vue' => true,
        'font_awesome' => 'fa-light fa-book',
        'route_name' => 'admin.nordihome.index',

    ],
];
