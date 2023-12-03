<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

class PeriodWeekEnabledDiscount extends EnabledDiscountAbstract
{

    public static function isEnabled(Discount $discount, float $cost = null): bool
    {
        if ((int)$discount->_from <= now()->dayOfWeek && now()->dayOfWeek <= (int)$discount->_to) return true;
        return false;
    }

    public static function name(): string
    {
        return 'По дням недели';
    }

    public static function caption(string $from_to): string
    {
        $weeks = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
        return $weeks[(int)$from_to];
    }

    public static function widget(): string
    {
        return view('admin.discount.discount.widget.period-week')->render();
    }
}
