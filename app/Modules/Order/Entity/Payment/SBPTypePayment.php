<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Payment;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;

class SBPTypePayment extends PaymentAbstract
{

    public static function online(): bool
    {
        return true;
    }

    public static function getPaidData(Order $order): string
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
        return '\images\payment\sbp.jpg';

    }

    public static function name(): string
    {
        return 'Оплата с помощью системы быстрых платежей';
    }

    public static function sort(): int
    {
        return 5;
    }

    public static function fields(array $fields = []): string
    {
        return '';
    }
}
