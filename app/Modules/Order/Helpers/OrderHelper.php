<?php
declare(strict_types=1);

namespace App\Modules\Order\Helpers;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderStatus;
use JetBrains\PhpStorm\ArrayShape;

class OrderHelper
{

    public static function pictogram(Order $order): string
    {
        $type = 'empty';
        $text = $order->statusHtml();
        if ($order->isAwaiting()) $type = 'warning';
        if ($order->isPaid()) $type = 'green';
        if ($order->isPrepaid()) $type = 'half-green';
        if ($order->isAwaiting() || $order->isPrepaid()) {
            if (!is_null($order->invoice) && $order->invoice->created_at->lte(now()->subDays(3))) {
                $type = 'red';
                $text = 'Оплата просрочена';
            }
        }
        if ($order->isPaid() && $order->getPaymentAmount() > $order->getTotalAmount()) {
            $type = 'double-green';
            $text = 'Переплата';
        };

        return '<span class="circle ' . $type . '" title="' . $text . '"></span>';
    }

    public static function status(Order $order): string
    {
        $text = $order->statusHtml();

        return '<span class="rounded-full">' . $text . '</span>';
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
