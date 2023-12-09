<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Order\Helpers\PaymentHelper;

class PaymentService
{

    public function get(int $user_id): array
    {
        //Получаем список всех платежных вариантов
        $payments = PaymentHelper::payments();
        //Получаем default для клиента
        $default = '';//'InvoiceTypePayment';
        if (isset($payments[$default])) $payments[$default]['sort'] = 0;

        usort($payments, function ($a, $b) {
            return $a['sort'] > $b['sort'];
        });
        //Сортировка if

        //Если default нет
        return $payments;
    }

    public function online(string $class)
    {
        return PaymentHelper::online($class);
    }

    public function invoice(string $class):string
    {
        $fields = [
            'INN' => '',
            'KPP' => '',
        ];
        return PaymentHelper::invoice($class, $fields);
    }
}
