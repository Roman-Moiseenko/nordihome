<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

use Carbon\Carbon;

class FullTimeEnabledDiscount extends EnabledDiscountAbstract
{

    public static function isEnabled(Discount $discount, int $cost = null): bool
    {
        if (Carbon::parse($discount->_from) <= now() && now() <= Carbon::parse($discount->_to)) return true;
        return false;
    }

    public static function name(): string
    {
        return 'По точному периоду';
    }

    public static function caption(string $from_to): string
    {
        return Carbon::parse($from_to)->translatedFormat('d F Y');
    }
    public static function widget(): string
    {
        return view('admin.discount.discount.widget.full-time')->render();
    }
}
