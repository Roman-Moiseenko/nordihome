<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Addition;

use App\Modules\Order\Entity\Order\Order;

class PackingCalculate extends CalculateAddition
{

    public static function calculate(Order $order, int $base): int
    {
        //TODO Сделать калькулятор расчета упаковки данные брать из того класса
        // $base - тип расчета
        $result = 0;
        foreach ($order->items as $item) {
            if ($item->packing) $result += $item->sell_cost * $item->quantity * 0.1;
        }
        return (int)ceil($result);
    }
}
