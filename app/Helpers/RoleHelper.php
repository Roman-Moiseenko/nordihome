<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entity\User\User;

class RoleHelper
{
    public static function html(User $user): string
    {
        if ($user->isUser()) return '<span class="badge bg-secondary">Клиент</span>';
        if ($user->isAdmin()) return '<span class="badge bg-danger">Администратор</span>';
        if ($user->isFinance()) return '<span class="badge bg-info">Финансист</span>';
        if ($user->isLogistics()) return '<span class="badge bg-primary ">Логист</span>';
        if ($user->isCashier()) return '<span class="badge bg-warning">Кассир</span>';
        if ($user->isCommodity()) return '<span class="badge bg-success">Товаровед</span>';
        return '';
    }
}
