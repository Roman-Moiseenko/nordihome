<?php

return [
    'feedback' => [
        'sort' => 80,
        'icon' => 'messages-square',
        'title' => '* Обратная связь (в разр.)',
        'can' => ['feedback', 'review'],
        'sub_menu' => [
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
