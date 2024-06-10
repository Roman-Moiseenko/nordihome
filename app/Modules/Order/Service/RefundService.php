<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Events\OrderHasRefund;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Analytics\LoggerService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderRefund;
use App\Modules\Order\Entity\Order\OrderStatus;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\Deprecated;

class RefundService
{

    private LoggerService $logger;

    public function __construct(LoggerService $logger)
    {
        $this->logger = $logger;
    }

    #[Deprecated]
    public function refund(Order $order, string $comment)
    {
        if ($order->getExpenseAmount() > 0) {
            $order->setStatus(OrderStatus::COMPLETED_REFUND);
        } else {
            $order->setStatus(OrderStatus::REFUND);
        }

        $order->clearReserve();//Возврат товаров в продажу
        //$this->create($order, $comment); //Создать документ на возврат
    }


    public function create(Order $order, array $params)
    {
        $items = $params['item'] ?? [];
        $additions = $params['addition'] ?? [];
        $payments = $params['payment'] ?? [];
        $comment = $params['comment'] ?? '';
        $number = $params['number'] ?? '';

        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $refund = OrderRefund::register($order->id, $staff->id, $comment, $number);

        $amount_items = 0;
        foreach ($items as $id => $quantity) {
            $refund->items()->create([
                'order_item_id' => $id,
                'quantity' => (int)$quantity,
            ]);
            /** @var OrderItem $itemOrder */
            $itemOrder = OrderItem::find($id);
            $amount_items += $itemOrder->sell_cost * (int)$quantity;
        }

        foreach ($additions as $id => $amount) {
            $refund->additions()->create([
                'order_addition_id' => $id,
                'amount' => (float)$amount,
            ]);
        }
        $amount_payments = 0;
        foreach ($payments as $id => $amount) {
            $refund->payments()->create([
                'order_payment_id' => (int)$id,
                'amount' => (float)$amount,
            ]);
            $amount_payments += (float)$amount;
        }

        //Проверка на сумму возврат
        if ($order->isCompleted())
        {
            if ($amount_payments != $amount_items)
                throw new \DomainException('Сумма возврата не совпадает со стоимостью возвращаемого товара');
        }
        if ($order->isPrepaid() || $order->isPaid())
        {
            if ($amount_payments != $order->getPaymentAmount() - $order->getExpenseAmount())
                throw new \DomainException('Сумма возврата не совпадает с переплатой');
        }
        $refund->amount = $amount_payments;
        $refund->save();

        $refund->refresh();
        event(new OrderHasRefund($order));

        $order->setStatus(OrderStatus::COMPLETED_REFUND);
        $order->clearReserve();//Возврат товаров в продажу
        $this->logger->logOrder($order, 'Заказ завершен с возвратом', '', '');

    }

    #[Deprecated]
    public function _create(Order $order, string $comment)
    {
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $refund = OrderRefund::register($order->id, $staff->id, $comment, '');

        foreach ($order->items as $item) {
            if ($remains = $item->getRemains()) {
                if ($remains != 0) {
                    $refund->items()->create([
                        'order_item_id' => $item->id,
                        'quantity' => $remains,
                    ]);
                }
            }
        }

        foreach ($order->additions as $addition) {
            if ($remains = $addition->getRemains()) {
                if ($remains != 0) {
                    $refund->additions()->create([
                        'order_addition_id' => $addition->id,
                        'amount' => $remains,
                    ]);
                }
            }
        }
        $refund->refresh();
        event(new OrderHasRefund($order));
    }
}
