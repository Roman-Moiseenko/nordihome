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
                'route_name' => 'admin.staff.show',
                'title' => 'Профиль',
            ],
            'password' => [
                'icon' => 'lock',
                'route_name' => 'admin.staff.edit',
                'title' => 'Сменить пароль',
            ],
            'mailing' => [
                'icon' => 'Edit',
                'route_name' => 'admin.staff.edit',
                'title' => 'Редактировать',
            ],
            'help' => [
                'icon' => 'help-circle',
                'route_name' => 'home',
                'title' => 'Помощь',
            ],
        ];
    }
}
