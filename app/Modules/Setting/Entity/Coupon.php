<?php
declare(strict_types=1);

namespace App\Modules\Setting\Entity;

class Coupon extends AbstractSetting
{
    public int $coupon = 0;
    public int $coupon_first_bonus = 0;
    public int $coupon_first_time = 0;
    public bool $bonus_review = false;
    public int $bonus_amount = 0;
    public int $bonus_discount_delay = 0;

    public function view()
    {
        return view('admin.settings.coupon', ['coupon' => $this]);
    }
}
