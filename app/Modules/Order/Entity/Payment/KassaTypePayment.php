<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Payment;

use App\Modules\Order\Entity\Order\Order;

class KassaTypePayment extends PaymentAbstract
{

    public static function online(): bool
    {
        return true;
    }

    public static function getPaidData(PaymentOrder $payment): string
    {
        //TODO Формирование ссылки на платеж
        $url = '/';
        return '<a href="' . $url . '" target="_blank">' . $url . '</a>';
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
        return 'Онлайн платеж через ЮКасса';
    }

    public static function sort(): int
    {
        return 6;
    }

    public static function fields(array $fields = []): string
    {
        return '';
    }
}
