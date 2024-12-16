<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

class CostEnabledDiscount extends EnabledDiscountAbstract
{

    public static function isEnabled(Discount $discount, float $cost = null): bool
    {
        if ((int)$discount->_from <= $cost && $cost <= (int)$discount->_to) return true;
        return false;
    }

    public static function name(): string
    {
        return 'По сумме покупки';
    }

    public static function caption(string $from_to): string
    {
        if (empty($from_to)) return '';
        return number_format((int)$from_to, 0, ',', ' ') . ' ₽';
    }

    public static function widget(): string
    {
        return view('admin.discount.discount.widget.cost')->render();
    }
}
