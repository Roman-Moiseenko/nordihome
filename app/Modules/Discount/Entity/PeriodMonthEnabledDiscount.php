<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

class PeriodMonthEnabledDiscount extends EnabledDiscountAbstract
{

    public static function isEnabled(Discount $discount, int $cost = null): bool
    {
        if ((int)$discount->_from <= now()->day && now()->day <= (int)$discount->_to) return true;
        return false;
    }

    public static function name(): string
    {
        return 'По числам месяца';
    }

    public static function caption(string $from_to): string
    {
        try {
            $day = (int)$from_to;
            return $from_to;
        } catch (\Throwable $e) {
            flash($e->getMessage(), 'danger');
            return '';
        }
    }

    public static function widget(): string
    {
        return view('admin.discount.discount.widget.period-month')->render();
    }
}
