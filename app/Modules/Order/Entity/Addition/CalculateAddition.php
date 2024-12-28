<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Addition;

use App\Modules\Order\Entity\Order\Order;

abstract class CalculateAddition
{
    const CLASSES = [
        DeliveryPolandCalculate::class => 'Доставка из польши v1.0',
        LiftingCalculate::class => 'Калькулятор подъема v1.0',
        PackingCalculate::class => 'Упаковка v1.0',
        AssemblyCalculate::class => 'Сборка (% от стоимости)',

    ];

    abstract public static function calculate(Order $order, int $base): int;

}
