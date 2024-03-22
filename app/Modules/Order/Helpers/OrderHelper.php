<?php
declare(strict_types=1);

namespace App\Modules\Order\Helpers;

use JetBrains\PhpStorm\ArrayShape;

class OrderHelper
{

    #[ArrayShape(['user' => "string[]", 'products' => "string[]", 'additions' => "string[]"])]
    public static function menuNewOrder(): array
    {
        return [
            'user' => [
                'include' => 'user',
                'caption' => 'Клиент',
                'anchor' => 'user'
            ],
            'products' => [
                'include' => 'products',
                'caption' => 'Товары',
                'anchor' => 'products'
            ],
            'additions' => [
                'include' => 'additions',
                'caption' => 'Дополнения',
                'anchor' => 'additions'
            ],
        ];
    }
}
