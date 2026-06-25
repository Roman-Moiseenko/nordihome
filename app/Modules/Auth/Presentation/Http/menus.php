<?php

declare(strict_types=1);

return [
    'staff' => [
        'sort'         => 1,
        'icon'         => 'contact',
        'title'        => 'Сотрудники',
        'route_name'   => 'admin.staff.index',
        'can'          => 'staff',
        'vue'          => true,
        'font_awesome' => 'fa-light fa-address-book',
    ],
    'role' => [
        'sort'         => 2,
        'icon'         => 'shield-check',
        'title'        => 'Роли',
        'route_name'   => 'admin.role.index',
        'can'          => 'staff',
        'vue'          => true,
        'font_awesome' => 'fa-light fa-shield-check',
    ],
];
