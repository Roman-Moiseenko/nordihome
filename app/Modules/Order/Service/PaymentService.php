<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Entity\Admin;
use App\Events\OrderHasCreated;
use App\Events\OrderHasPaid;
use App\Events\OrderHasPrepaid;
use App\Events\PaymentHasPaid;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\Payment\PaymentHelper;
use App\Modules\Order\Entity\UserPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function create(Request $request): OrderPayment
    {
        /** @var Order $order */
        $order = Order::find((int)$request['order']);
        if ($order->status->value != OrderStatus::AWAITING && $order->status->value != OrderStatus::PREPAID)
            throw new \DomainException('Нельзя внести платеж за заказ!');
        $payment = OrderPayment::new((float)$request['amount'], $request['method'], $request['document'] ?? '');
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $payment->staff_id = $staff->id;
        $order->payments()->save($payment);
        $order->refresh();
        if ($order->getTotalAmount() <= $order->getPaymentAmount()) {
            $order->setPaid();
            event(new OrderHasPaid($order));
        }
        if ($order->status->value == OrderStatus::AWAITING) {
            $order->setStatus(OrderStatus::PREPAID);
            event(new OrderHasPrepaid($order));
        }
        return $payment;
    }

    /**
     * Платеж онлайн, проработать после подключения
     * @param array $data
     * @return void
     */
    public function create_online(array $data)
    {
        /** @var Order $order */
        $order = Order::find((int)$data['order_id']);
        $payment = OrderPayment::new((float)$data['amount'], $data['method'], $data['document'] ?? '');

        $order->payments()->save($payment);
        $order->refresh();
        if ($order->getTotalAmount() <= $order->getPaymentAmount()) {
            $order->setPaid();
            event(new OrderHasPaid($order));
        }
        if ($order->status->value == OrderStatus::AWAITING) {
            $order->setStatus(OrderStatus::PREPAID);
            event(new OrderHasPrepaid($order));
        }
    }


    public function update(OrderPayment $payment, Request $request): OrderPayment
    {
        $order = $payment->order;
        if ($order->status->value != OrderStatus::PREPAID)
            throw new \DomainException('Нельзя внести изменения в платеж за заказ!');
        //TODO Подумать, можно ли вносить изменения в сумму
        $payment->amount = (float)$request['amount'];
        $payment->method = $request['method'];
        $payment->document = $request['document'] ?? '';
        $payment->save();
        $payment->refresh();
        return $payment;
    }

    public function destroy(OrderPayment $payment)
    {
        $order = $payment->order;
        if ($order->status->value != OrderStatus::PREPAID)
            throw new \DomainException('Нельзя удалить платеж за заказ!');
        $payment->delete();
        $order->refresh();
        if ($order->getPaymentAmount() == 0) {
            OrderStatus::where('value', OrderStatus::PREPAID)->where('order_id', $order->id)->delete();
        }
    }


}
