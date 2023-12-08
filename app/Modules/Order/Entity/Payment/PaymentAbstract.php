<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Payment;

abstract class PaymentAbstract
{
    abstract public static function online(): bool;
    abstract public static function getPaidData(): string;

    abstract public static function toPay(): void;

    abstract public static function image(): string;
    abstract public static function name(): string;
    abstract public function sort(): int;
}
