<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity\Transport;

use App\Modules\Cart\Domain\Entities\CartItemEntity;
use JetBrains\PhpStorm\Deprecated;

#[Deprecated]
class PostTypeDelivery extends DeliveryAbstract
{

    public static function name()
    {
        return 'Почта России';
    }

    public static function image()
    {
        return '/images/delivery/post.jpg';
    }

    /**
     * @param CartItemEntity[] $items
     */
    public static function calculate(array $items, array $params): DeliveryData
    {
        return new DeliveryData(700, 25);
    }
}
