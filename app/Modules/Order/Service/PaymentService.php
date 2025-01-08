<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Events\PaymentHasPaid;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Analytics\LoggerService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\Payment\PaymentHelper;
use App\Modules\User\Entity\UserPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\Deprecated;

#[Deprecated]
class PaymentService
{

    private LoggerService $logger;
    private OrderService $orderService;

    public function __construct(LoggerService $logger, OrderService $orderService)
    {
        $this->logger = $logger;
        $this->orderService = $orderService;
    }

    public function create(array $request): OrderPayment
    {
        DB::transaction(function () use ($request, &$payment) {
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
            $this->orderService->checkPayment($order);

            $this->logger->logOrder($order, 'Внесена оплата', $payment->methodHTML(), price($payment->amount));
        });

        return $payment;
    }

    /**
     * Платеж онлайн, проработать после подключения
     * @param array $data
     * @return void
     */
    public function create_online(array $data)
    {
        DB::transaction(function () use ($data) {
            /** @var Order $order */
            $order = Order::find((int)$data['order_id']);
            $payment = OrderPayment::new((float)$data['amount'], $data['method'], $data['document'] ?? '');

            $order->payments()->save($payment);
            $order->refresh();
            $this->logger->logOrder($order, 'Внесена оплата', $payment->methodHTML(), price($payment->amount));

            $this->orderService->checkPayment($order);

            event(new PaymentHasPaid($payment));
        });
    }

    public function update(OrderPayment $payment, Request $request): OrderPayment
    {
        $order = $payment->order;
        $amount = $request->float('amount');
        if ($amount == 0) throw new \DomainException('Сумма не может быть равна нулю!');
        if ($order->status->value != OrderStatus::PREPAID)
            throw new \DomainException('Нельзя внести изменения в платеж за заказ!');

        if ($payment->amount != $amount) {
            $old_value = price($payment->amount);
            $payment->amount = $amount;
            $new_value = price($payment->amount);
            $this->logger->logOrder($order, 'Изменена сумма оплаты', $old_value, $new_value);
        }

        if ($payment->method != $request['method']) {
            $old_value = $payment->methodHTML();
            $payment->method = $request['method'];
            $new_value = $payment->methodHTML();
            $this->logger->logOrder($order, 'Изменен способ оплаты', $old_value, $new_value);
        }

        $payment->document = $request->string('document')->trim()->value();
        $payment->save();
        $payment->refresh();

        return $payment;
    }

    public function destroy(OrderPayment $payment)
    {
        $order = $payment->order;
        if ($order->status->value != OrderStatus::PREPAID)
            throw new \DomainException('Нельзя удалить платеж за заказ!');
        $this->logger->logOrder($order, 'Удален платеж', $payment->methodHTML(), price($payment->amount));
        $payment->delete();
        $order->refresh();
        if ($order->getPaymentAmount() == 0) {
            OrderStatus::where('value', OrderStatus::PREPAID)->where('order_id', $order->id)->delete();
        }
    }


}
