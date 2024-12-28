<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Addition;

use App\Modules\Order\Entity\Order\Order;

class DeliveryPolandCalculate extends CalculateAddition
{
    public static function calculate(Order $order, int $base): int
    {
        //TODO Обсчет доставки по поставщику/бренду от веса и объема и другое
        return 999;
    }
}
