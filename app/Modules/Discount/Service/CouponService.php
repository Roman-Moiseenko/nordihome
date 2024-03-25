<?php
declare(strict_types=1);

namespace App\Modules\Discount\Service;

use App\Modules\Discount\Entity\Coupon;
use App\Modules\Order\Entity\Order\Order;

class CouponService
{
    //TODO Купоны считаем
    public function __construct()
    {

    }

     function discount(?Coupon $coupon, Order $order): float
    {
        return 0.0;
    }
}
