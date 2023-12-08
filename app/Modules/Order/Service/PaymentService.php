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

        //Если default нет
        return $payments;
    }
}
