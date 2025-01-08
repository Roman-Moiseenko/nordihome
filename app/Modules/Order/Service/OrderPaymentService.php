<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Analytics\LoggerService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderPayment;
use Auth;

class OrderPaymentService
{
    private LoggerService $logger;
    private OrderService $orderService;

    public function __construct(LoggerService $logger, OrderService $orderService)
    {
        $this->logger = $logger;
        $this->orderService = $orderService;
    }

    public function create(Order $order, float $amount, int $method): OrderPayment
    {
        $payment = OrderPayment::new($amount, $method);
        $staff = Auth::guard('admin')->user();
        $payment->staff_id = $staff->id;
        $order->payments()->save($payment);

        $order->refresh();
        $this->orderService->checkPayment($order);

        $this->logger->logOrder($order, 'Внесена оплата', $payment->methodText(), price($payment->amount));

        return $payment;
    }
}
