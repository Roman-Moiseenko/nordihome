<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

abstract class EnabledDiscountAbstract
{
    abstract public static function isEnabled(Discount $discount, int $cost = null): bool;
    abstract public static function name(): string;
    abstract public static function caption(string $from_to): string;

    public function type(): string
    {
        return static::class;
    }
}
