<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Payment;

class KassaTypePayment extends PaymentAbstract
{

    public static function online(): bool
    {
        return true;
    }

    public static function getPaidData(): string
    {
        return '';
    }

    public static function toPay(): void
    {
        // TODO: Implement toPay() method.
    }

    public static function image(): string
    {
        return '\images\payment\kassa.jpg';
    }

    public static function name(): string
    {
        return 'Оплата с помощью системы быстрых платежей';
    }

    public function sort(): int
    {
        return 6;
    }
}
