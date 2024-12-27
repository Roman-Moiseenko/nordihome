<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Addition;

use App\Modules\Order\Entity\Order\Order;

interface CalculateInterface
{
    public static function calculate(Order $order, int $base): int;
}
