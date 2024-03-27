<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Events\OrderHasCreated;
use App\Events\PaymentHasPaid;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Entity\Payment\PaymentHelper;
use App\Modules\Order\Entity\UserPayment;

class PaymentService
{
    public function user(int $user_id): UserPayment
    {
        if ($user = UserPayment::where('user_id', $user_id)->first()) return $user;
        return UserPayment::register($user_id);
    }

    public function get(): array
    {
        //Получаем список всех платежных вариантов
        $payments = PaymentHelper::payments();
        usort($payments, function ($a, $b) {
            return $a['sort'] > $b['sort'];
        });
        return $payments;
    }




}
