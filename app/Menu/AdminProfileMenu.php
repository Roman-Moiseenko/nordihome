<?php
declare(strict_types=1);

namespace App\Menu;

class AdminProfileMenu
{
    public static function menu(): array
    {
        return [
            'profile' => [
                'icon' => 'user',
                'route_name' => 'home',
                'title' => 'Профиль',
            ],
            'password' => [
                'icon' => 'lock',
                'route_name' => 'home',
                'title' => 'Сменить пароль',
            ],
            'mailing' => [
                'icon' => 'Edit',
                'route_name' => 'home',
                'title' => 'Редактировать (?)',
            ],
            'help' => [
                'icon' => 'help-circle',
                'route_name' => 'home',
                'title' => 'Помощь',
            ],
           /* 'logout' => [
                'icon' => 'toggle-right',
                'route_name' => 'logout',
                'title' => 'Выход',
            ],*/
        ];
    }
}
