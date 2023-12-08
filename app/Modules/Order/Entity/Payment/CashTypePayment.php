<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Payment;

class CashTypePayment extends PaymentAbstract
{

    public static function online(): bool
    {
        return false;
    }

    public static function getPaidData(): string
    {
        return '';
    }

    public static function toPay(): void
    {

    }

    public static function image(): string
    {
        return '/images/payment/cash.png';
    }

    public static function name(): string
    {
        return 'Наличными при получении';
    }

    public function sort(): int
    {
        return 1;
    }
}
