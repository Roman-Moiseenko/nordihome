<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin menu items for Analytics module
|--------------------------------------------------------------------------
|
| Каждый пункт:
|   - sort:        int (порядок в сайдбаре)
|   - icon:        string (Lucide icon name)
|   - title:       string (текст в сайдбаре)
|   - route_name:  string (named route)
|   - can:         string (permission gate)
|   - vue:         bool (Vue/Inertia frontend)
|   - font_awesome: string (Font Awesome class)
|
*/

return [
    'analytics' => [
        'sort' => 110,
        'icon' => 'scroll-text',
        'title' => 'Аналитика',
        'can' => 'admin-panel',
        'font_awesome' => 'fa-light fa-chart-line',
        'route_name' => 'admin.analytics.dashboard',
        'sub_menu' => [
            'dashboard' => [
                'icon' => 'gauge',
                'title' => 'Обзор',
                'route_name' => 'admin.analytics.dashboard',
                'vue' => true,
                'font_awesome' => 'fa-light fa-gauge-high',
            ],
            'searches' => [
                'icon' => 'search',
                'title' => 'Поиск',
                'route_name' => 'admin.analytics.searches',
                'vue' => true,
                'font_awesome' => 'fa-light fa-magnifying-glass',
            ],
            'pages' => [
                'icon' => 'files',
                'title' => 'Страницы',
                'route_name' => 'admin.analytics.pages',
                'vue' => true,
                'font_awesome' => 'fa-light fa-file-lines',
            ],
            'actions' => [
                'icon' => 'pointer',
                'title' => 'Действия',
                'route_name' => 'admin.analytics.actions',
                'vue' => true,
                'font_awesome' => 'fa-light fa-hand-pointer',
            ],
            'sources' => [
                'icon' => 'route',
                'title' => 'Источники',
                'route_name' => 'admin.analytics.sources',
                'vue' => true,
                'font_awesome' => 'fa-light fa-route',
            ],
            'audience' => [
                'icon' => 'users',
                'title' => 'Аудитория',
                'route_name' => 'admin.analytics.audience',
                'vue' => true,
                'font_awesome' => 'fa-light fa-users',
            ],
            'funnel' => [
                'icon' => 'filter',
                'title' => 'Воронка',
                'route_name' => 'admin.analytics.funnel',
                'vue' => true,
                'font_awesome' => 'fa-light fa-filter',
            ],
        ],
    ],
];
