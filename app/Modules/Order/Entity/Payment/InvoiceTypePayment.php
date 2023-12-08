<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Payment;

class InvoiceTypePayment extends PaymentAbstract
{

    public static function online(): bool
    {
        return false;
    }

    public static function getPaidData(): string
    {
        return 'Файл счета';
    }

    public static function toPay(): void
    {
        // TODO: Implement toPay() method.
    }

    public static function image(): string
    {
        return '\images\payment\invoice.png';
    }

    public static function name(): string
    {
        return 'Счет для юридического лица';
    }

    public function sort(): int
    {
        return 4;
    }
}
