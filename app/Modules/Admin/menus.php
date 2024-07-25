<?php

return [
    'staff' => [
        'sort' => 1,
        'icon' => 'contact',
        'title' => 'Сотрудники',
        'route_name' => 'admin.staff.index',
        'can' => 'staff',
    ],
    'worker' => [
        'sort' => 2,
        'icon' => 'anvil',
        'title' => 'Рабочие',
        'route_name' => 'admin.worker.index',
        'can' => 'staff',
    ],
    'task' => [
        'sort' => 70,
        'icon' => 'clipboard-check',
        'title' => 'Задачи',
        'can' => '',
        'sub_menu' => [
            'notification' => [
                'icon' => 'bell-ring',
                'title' => 'Уведомления',
                'route_name' => 'admin.staff.notification',
                'action' => true,
            ],
            'mail' => [
                'icon' => 'mail',
                'title' => 'Почта',
                'route_name' => 'admin.home',
            ],
        ],
    ],
];
