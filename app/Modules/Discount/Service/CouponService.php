<?php
declare(strict_types=1);

namespace App\Modules\Discount\Service;

use App\Modules\Discount\Entity\Coupon;

class CouponService
{
    //TODO Купоны считаем
    public function __construct()
    {

    }

     function discount(?Coupon $coupon, array $getItems): float
    {
        return 0.0;
    }
}
