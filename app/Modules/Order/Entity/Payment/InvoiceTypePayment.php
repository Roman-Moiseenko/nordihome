<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Payment;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;

class InvoiceTypePayment extends PaymentAbstract
{

    public static function online(): bool
    {
        return false;
    }

    public static function getPaidData(Order $order): string
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

    public static function sort(): int
    {
        return 4;
    }

    public static function isInvoice(): bool
    {
        return true;
    }

    public static function getInvoiceData(string $inn) {
        //TODO Поиск по API данных об организации
        return [
            'INN' => $inn,
            'KPP' => '3900000',
            'name' => 'ООО Рога и Копыта',

        ];
    }
}
