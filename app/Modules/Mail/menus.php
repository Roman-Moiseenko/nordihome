<?php

return [
    'mails' => [
        'sort' => 80,
        'title' => 'Почта',
        'can' => '',
        'vue' => true,
        'font_awesome' => 'fa-light fa-mailbox',

        'sub_menu' => [
            'create' => [
                'icon' => 'Edit',
                'title' => 'Написать',
                'route_name' => 'admin.mail.outbox.create',
                'vue' => true,
                'font_awesome' => 'fa-light fa-envelope',
            ],
            'outbox' => [
                'icon' => 'Position',
                'title' => 'Исходящие',
                'route_name' => 'admin.mail.outbox.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-inbox-out',
            ],
            'inbox' => [
                'icon' => 'Message',
                'title' => 'Входящие',
                'route_name' => 'admin.mail.inbox.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-inbox-in',
            ],
            'system' => [
                'icon' => 'Setting',
                'title' => 'Системная',
                'route_name' => 'admin.mail.system.index',
                'vue' => true,
                'font_awesome' => 'fa-light fa-folder-gear',
            ],
        ],

    ],
];
