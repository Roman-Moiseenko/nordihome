<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Addition;

use App\Modules\Order\Entity\Order\Order;

class LiftingCalculate extends CalculateAddition
{
    /**
     * @param Order $order
     * @param int $base - стоимость поднятия 1 кг за этаж
     * @return int
     */
    public static function calculate(Order $order, int $base): int
    {
        $result = 0;
        foreach ($order->items as $item) {
            $result += $item->product->weight() * $item->quantity;
        }
        if ($result < 1) $result = 1;
        return (int)ceil($result * $base);
    }
}
