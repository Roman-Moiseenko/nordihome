<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin menu items for Feedback module
|--------------------------------------------------------------------------
|
| Register menu items following the format below.
| Replace 'Feedback' with the plural form (e.g., 'users', 'pages').
|
| Each item requires:
|   - sort:        int (sorting order in sidebar)
|   - icon:        string (Lucide icon name, e.g. 'users', 'settings')
|   - title:       string (display text in sidebar)
|   - route_name:  string (named route, e.g. 'admin.feedback.index')
|   - can:         string (permission gate, e.g. 'staff', 'pages')
|   - vue:         bool (uses Vue/Inertia frontend)
|   - font_awesome: string (Font Awesome class, e.g. 'fa-light fa-users')
|
*/

return [
    'feedback' => [
        'sort' => 80,
        'icon' => 'messages-square',
        'title' => '* Обратная связь (в разр.)',
        'can' => ['feedback', 'review'],
        'sub_menu' => [
            'forms' => [
                'title' => 'Формы (ответы)',
                'route_name' => 'admin.feedback.form.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-pen-field',
            ],
            'review' => [
                'icon' => 'message-square-warning',
                'title' => 'Отзывы',
                'route_name' => 'admin.home',
                'can' => 'review',
            ],
            'mail' => [
                'icon' => 'mail',
                'title' => 'Жалобы клиентов',
                'route_name' => 'admin.home',
                'can' => 'message-circle-warning',
            ],
        ],
    ],
    'feedback_divider' => [
        'sort' => 81,
    ],
];
