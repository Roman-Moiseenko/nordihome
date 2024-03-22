<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Payment;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;

class CriptoTypePayment extends PaymentAbstract
{

    public static function online(): bool
    {
        return false;
    }

    public static function getPaidData(Order $order = null): string
    {
        return 'Данные крипто кошелька';
    }

    public static function toPay(): void
    {
        // TODO: Implement toPay() method.
    }

    public static function image(): string
    {
        return '\images\payment\cripto.png';
    }

    public static function name(): string
    {
        return 'Оплата крипто валютой';
    }

    public static function sort(): int
    {
        return 3;
    }

    public static function fields(array $fields = []): string
    {
        return '';
    }
}
