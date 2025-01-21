<?php

if (config('shop.theme') != 'nbrussia') return [];
return [
    'nbrussia' => [
        'sort' => 200,
        'icon' => 'database',
        'title' => 'NB Russia *',
        'can' => '',
        'vue' => true,
        'font_awesome' => 'fa-light fa-book',
        'route_name' => 'admin.nbrussia.parser.index',

    ],
];
