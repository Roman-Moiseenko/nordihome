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

    public static function sort(): int
    {
        return 4;
    }

    public static function fields(array $fields = []): string
    {
        return '
<h4>Необходимо заполнить данные:</h4>
        <div class="payment-invoice-data">
            <label for="inn">ИНН Плательшика</label>
            <input id="inn" class="form-control form-invoice inn" name="INN" type="text" value="'. $fields['INN'] .'" placeholder="ИНН" state="0">
        </div>
        ';
    }
}
