<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Addition;

use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Infrastructure\Models\Order;

class AssemblyCalculate extends CalculateAddition
{
    /**
     * @param Order $order
     * @param int $base - % от продажной стоимости
     * @return int
     */
    public static function calculate(Order $order, int $base): int
    {
        /*$settings = new SettingRepository();
        $common = $settings->getCommon();*/
        $result = 0;
        foreach ($order->items as $item) {
            if ($item->assemblage) $result += $item->sell_cost * $item->quantity * $base / 100;
        }
        return (int)ceil($result);
    }

    public static function calculateEntity(OrderEntity $order, int $base): int
    {
        $result = 0;
        foreach ($order->items as $item) {
            if ($item->assemblage) $result += $item->sellCost * $item->quantity * $base / 100;
        }
        return (int)ceil($result);
    }
}
