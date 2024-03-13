<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Events\OrderHasCreated;
use App\Events\PaymentHasPaid;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Payment\PaymentOrder;
use App\Modules\Order\Entity\UserPayment;
use App\Modules\Order\Helpers\PaymentHelper;

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

    /**
     * Платеж был оплачен.
     * Вызов - Вручную (наличные, переводы) и Чз сервисы оплаты, посредством фасада по приему платежей
     * @param PaymentOrder $payment
     * @return void
     */
    public function payed(PaymentOrder $payment)
    {
        //TODO
       $payment->paid_at = now();
       $payment->save();
       event(new PaymentHasPaid($payment));
    }


    public function create(Order $order)
    {
        $payment = PaymentOrder::new($order->id, $this->user($order->user_id)->class_payment, PaymentOrder::PAY_ORDER);

    }

    /**
     * Слушатель события создания Заказа
     * @param OrderHasCreated $event
     * @return void
     */
    public function handle(OrderHasCreated $event): void
    {
        try {
            $order = $event->order;
            $payment = PaymentOrder::new($order->total, $this->user($order->user_id)->class_payment,PaymentOrder::PAY_ORDER);
            $order->payments()->save($payment);
        } catch (\Throwable $e) {
            flash($e->getMessage());
        }

       // event($payment);
    }

}
