<?php

return [
    'unloads' => [
        'sort' => 96,
        'icon' => 'settings',
        'title' => 'Выгрузки',
        'can' => 'admin-panel',
        'font_awesome' => 'fa-light fa-cloud-arrow-up',
        'sub_menu' => [
            'fids' => [
                'title' => 'Фиды',
                'route_name' => 'admin.unload.feed.index',
                'font_awesome' => 'fa-light fa-rss',
            ],

        ],
    ],
];
