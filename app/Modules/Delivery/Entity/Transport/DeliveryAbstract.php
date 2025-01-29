<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity\Transport;

use JetBrains\PhpStorm\Deprecated;

#[Deprecated]
abstract class DeliveryAbstract
{
    abstract public static function name();
    abstract public static function image();
    abstract public static function calculate(array $items, array $params): DeliveryData;
}
