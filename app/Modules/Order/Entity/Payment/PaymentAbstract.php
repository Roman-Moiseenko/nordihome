<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Payment;

use App\Modules\Order\Entity\Order\Order;

abstract class PaymentAbstract
{
    abstract public static function online(): bool;
    abstract public static function getPaidData(PaymentOrder $payment);

    abstract public static function toPay(): void;

    abstract public static function image(): string;
    abstract public static function name(): string;
    abstract public static function sort(): int;

    public static function isInvoice(): bool
    {
        return false;
    }

    public static function getInvoiceData(string $inn) {
        return [];
    }
}
