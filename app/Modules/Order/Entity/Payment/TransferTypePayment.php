<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Payment;

class TransferTypePayment extends PaymentAbstract
{

    public static function online(): bool
    {
        return false;
    }

    public static function getPaidData(): string
    {
        return 'Данные карты';
    }

    public static function toPay(): void
    {
        // TODO: Implement toPay() method.
    }

    public static function image(): string
    {
        return '\images\payment\transfer.png';
    }

    public static function name(): string
    {
        return 'Денежный перевод';
    }

    public function sort(): int
    {
        return 2;
    }
}
