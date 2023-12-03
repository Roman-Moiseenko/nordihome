<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

abstract class EnabledDiscountAbstract
{
    abstract public static function isEnabled(Discount $discount, float $cost = null): bool;
    abstract public static function name(): string;
    abstract public static function caption(string $from_to): string;
    abstract public static function widget(): string;

    public function type(): string
    {
        return static::class;
    }
}
