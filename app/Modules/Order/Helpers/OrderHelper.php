<?php
declare(strict_types=1);

namespace App\Modules\Order\Helpers;

use App\Modules\Order\Entity\Order\Order;
use JetBrains\PhpStorm\ArrayShape;

class OrderHelper
{

    public static function pictogram(Order $order): string
    {
        $type = 'empty';

        if ($order->isAwaiting()) $type = 'warning';
        if ($order->isPaid()) $type = 'green';
        if ($order->isPrepaid()) $type = 'half-green';
        if ($order->isAwaiting() || $order->isPrepaid()) {
            if (!is_null($order->invoice) && $order->invoice->created_at->lte(now()->subDays(3)))
                $type = 'red';
        }
        if ($order->isPaid() && $order->getPaymentAmount() > $order->getTotalAmount()) $type = 'double-green';

        return '<span class="circle ' . $type . '"></span>';
    }

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

    #[ArrayShape(['info' => "string[]", 'products' => "string[]", 'additions' => "string[]", 'payments' => "string[]"])]
    public static function menuCreateOrder(): array
    {
        return [
            'info' => [
                'include' => 'info',
                'caption' => 'Информация',
                'anchor' => 'info'
            ],
            'products' => [
                'include' => 'products',
                'caption' => 'Товары',
                'anchor' => 'products'
            ],
            'additions' => [
                'include' => 'additions',
                'caption' => 'Услуги',
                'anchor' => 'additions'
            ],
            'payments' => [
                'include' => 'payments',
                'caption' => 'Платежи по заказу',
                'anchor' => 'payments'
            ],
        ];
    }
}
