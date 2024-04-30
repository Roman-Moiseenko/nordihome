<?php
declare(strict_types=1);

namespace App\Modules\Discount\Service;

use App\Modules\Admin\Entity\Options;
use App\Modules\Discount\Entity\Coupon;
use App\Modules\Order\Entity\Order\Order;
use JetBrains\PhpStorm\Pure;

class CouponService
{
    //TODO Купоны считаем
    private $coupon;

    public function __construct()
    {
        $this->coupon = (new Options())->shop->coupon;
    }

    #[Pure]
    function discount(?Coupon $coupon, Order $order): float
    {
        if (is_null($coupon)) return 0;
        if ($coupon->min_amount > $order->getBaseAmount()) return 0;
        return min($coupon->bonus, (int)ceil($order->getBaseAmount() * $this->coupon / 100));
    }
}
