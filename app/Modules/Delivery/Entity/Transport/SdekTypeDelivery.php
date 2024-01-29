<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity\Transport;

use App\Modules\Shop\CartItemInterface;

class SdekTypeDelivery extends DeliveryAbstract
{

    public static function name()
    {
        return 'СДЕК';
    }

    public static function image()
    {
        return '/images/delivery/sdek.jpg';
    }

    /**
     * @param CartItemInterface[] $items
     */
    public static function calculate(array $items, array $params): DeliveryData
    {
        return new DeliveryData(1000, 15);
    }
}
