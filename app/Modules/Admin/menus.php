<?php

return [
    'worker' => [
        'sort' => 3,
        'icon' => 'anvil',
        'title' => 'Рабочие',
        'route_name' => 'admin.worker.index',
        'can' => 'staff',
        'vue' => true,
        'font_awesome' => 'fa-light fa-user-helmet-safety'
    ],
   /* 'task' => [
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
        ],
    ],*/
];
