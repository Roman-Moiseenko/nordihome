<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

use Carbon\Carbon;

class PeriodYearEnabledDiscount extends EnabledDiscountAbstract
{

    public static function isEnabled(Discount $discount, float $cost = null): bool
    {
        $year = now()->format('Y');
        if (Carbon::parse($discount->_from . ' ' . $year) <= now() && now() <= Carbon::parse($discount->_to . ' ' . $year)) return true;
        return false;
    }

    public static function name(): string
    {
        return 'По периоду текущего года';
    }

    public static function caption(string $from_to): string
    {
        return Carbon::parse('2000-' . $from_to)->translatedFormat('d F');
    }
    public static function widget(): string
    {
        return view('admin.discount.discount.widget.period-year')->render();
    }
}
