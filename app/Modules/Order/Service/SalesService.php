<?php


namespace App\Modules\Order\Service;


use App\Entity\Admin;
use App\Events\MovementHasCreated;
use App\Events\OrderHasCanceled;
use App\Events\OrderHasLogger;
use App\Events\PointHasEstablished;
use App\Events\ThrowableHasAppeared;
use App\Mail\OrderAwaiting;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderResponsible;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\Payment\PaymentOrder;
use App\Modules\Order\Entity\UserPayment;
use App\Modules\Shop\Calculate\CalculatorOrder;
use App\Modules\Shop\Cart\Cart;
use App\Modules\Shop\Cart\CartItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SalesService
{

    private ReserveService $reserve;
    private MovementService $movements;

    public function __construct(ReserveService $reserve, MovementService $movements)
    {
        $this->reserve = $reserve;
        $this->movements = $movements;
    }

    public function setManager(Order $order, int $staff_id)
    {
        $staff = Admin::find($staff_id);
        if (empty($staff)) throw new \DomainException('Менеджер под ID ' . $staff_id . ' не существует!');
        $order->setStatus(OrderStatus::SET_MANAGER);
        $order->responsible()->save(OrderResponsible::registerManager($staff->id));
    }

    public function setReserve(Order $order, string $date, string $time)
    {
        $new_reserve = $date . ' ' . $time . ':00';
        foreach ($order->items as $item) {
            $item->reserve->update([
                'reserve_at' => $new_reserve,
            ]);
        }
    }

    /**
     * Устанавливаем новое кол-во товаров для Заказа, снимаем лишнее с резерва и пересчитываем заказ, с учетом скидок и акций
     * @param Order $order
     * @param array $items
     * @return bool
     */
    public function setQuantity(Order $order, array $items): bool
    {
        $result = false;

        foreach ($items as $item) {
            $new_quantity = (int)$item['quantity'];
            /** @var OrderItem $orderItem */
            $orderItem = OrderItem::find((int)$item['id']);
            //Если кол-во изменилось
            if ($orderItem->quantity != $new_quantity) {
                $result = true; //Хотя бы одно изменение кол-ва.
                //Снимаем с резерва
                $sub_reserve = $orderItem->quantity - $new_quantity;
                $this->reserve->subReserve($orderItem->reserve->id, $sub_reserve);
                $orderItem->changeQuantity($new_quantity);
            }
        }
        if ($result == false) return false;
        $order->refresh();
        //Пересчет скидок и стоимости Заказа
        $cartItems = [];
        foreach ($order->items as $item) {
            $cartItems[] = CartItem::create(
                product: $item->product,
                quantity: $item->quantity,
                options: [],
                check_quantity: false);
        }
        $calculator = new CalculatorOrder();
        $cartItems = $calculator->calculate($cartItems);
        $order->amount = 0;
        $order->total = 0;
        foreach ($cartItems as $cartItem) {
            $orderItem = $order->getItem($cartItem->product);
            if (isset($cartItem->discount_id)) {
                $orderItem->discount_id = $cartItem->discount_id;
                $orderItem->discount_type = $cartItem->discount_type;
                $orderItem->sell_cost = $cartItem->discount_cost;
            }
            $orderItem->save();
            $orderItem->refresh();
            $order->amount += $orderItem->quantity * $orderItem->base_cost;
            $order->total += $orderItem->quantity * $orderItem->sell_cost;
        }
        $order->discount = $order->amount - $order->total - $order->coupon;
        $order->save();

        return true;
    }

    public function setAwaiting(Order $order)
    {
        //Проверить что установлена точка выдачи
        if (!$order->isPoint()) throw new \DomainException('Не установлена точка сбора/выдачи!');

        //Проверить, если на доставку
        if (!$order->delivery->isStorage()) {
            if ($order->delivery->cost == 0) throw new \DomainException('Не установлена стоимость доставка');
            $payment_delivery = 0;
            foreach ($order->payments as $payment) {
                if ($payment->purpose == PaymentOrder::PAY_DELIVERY) {
                    $payment_delivery += $payment->amount;
                }
            }
            if ($payment_delivery < $order->delivery->cost) throw new \DomainException('Сумма платежей по доставке меньше ее стоимости');
        }
        if (empty($order->payments)) throw new \DomainException('Нет ни одного платежа');

        //Проверить на сумму платежа за заказ
        $payment_order = 0;
        foreach ($order->payments as $payment) {
            if ($payment->purpose == PaymentOrder::PAY_ORDER) {
                $payment_order += $payment->amount;
            }
        }
        if ($payment_order != $order->total) throw new \DomainException('Сумма платежей за заказ не совпадает со стоимостью заказа');

        $order->setStatus(OrderStatus::AWAITING);
        foreach ($order->payments as $payment) {
            if (!empty($paymentDocument = $payment->createOnlinePayment())) {
                $payment->document = $paymentDocument;
                $payment->save();
            }
        }
        Mail::to($order->user->email)->queue(new OrderAwaiting($order));
    }

    /**
     * Установка вручную стоимости доставки
     * @param Order $order
     * @param float $cost
     * @return void
     */
    public function setDelivery(Order $order, float $cost)
    {
        /** @var UserPayment $userPayment */
        $userPayment = UserPayment::where('user_id', $order->user_id)->first();

        if ($order->delivery->cost != 0) {
            $pay = PaymentOrder::where('order_id', $order->id)->where('amount', $order->delivery->cost)->first();
            if (!empty($pay)) $pay->delete();
        }

        $order->delivery->cost = $cost;
        $order->delivery->save();

        $payment = PaymentOrder::new($cost, $userPayment->class_payment, PaymentOrder::PAY_DELIVERY);
        $order->payments()->save($payment);
    }

    public function setLogger(Order $order, int $logger_id)
    {
        $logger = Admin::find($logger_id);
        if (empty($logger)) throw new \DomainException('Сборщик под ID ' . $logger_id . ' не существует!');
        $order->responsible()->save(OrderResponsible::registerLogger($logger->id));
        $order->setStatus(OrderStatus::ORDER_SERVICE);
        event(new OrderHasLogger($order));
    }

    /**
     * Устанавливаем точку сборки заказа и при необходимости формируем заявку на перемещение товара
     * @param Order $order
     * @param int $storage_id
     * @return void
     */
    public function setMoving(Order $order, int $storage_id)
    {
        $storage = Storage::find($storage_id);
        $order->setPoint($storage->id); // Установить точку выдачи товара
        $order->setStorage($storage->id); // в резервах указать склад,
        $movements = $this->movements->createByOrder($order); //Создаем перемещения, если нехватает товара
        event(new PointHasEstablished($order));
        if (!is_null($movements)) event(new MovementHasCreated($movements));
    }

    public function destroy(Order $order)
    {
        if ($order->status->value == OrderStatus::FORMED) {
            $order->delete();
        } else {
            throw new \DomainException('Нельзя удалить заказ, который уже в работе');
        }
    }

    public function canceled(Order $order, string $comment)
    {
        foreach ($order->items as $item) {
            $this->reserve->delete($item->reserve);
        }
        $order->setStatus(value: OrderStatus::CANCEL, comment: $comment);
        event(new OrderHasCanceled($order));
    }

    public function setPayment(Order $order, array $params)
    {
        $payment = PaymentOrder::new(
            amount: (float)$params['payment-amount'],
            class: $params['payment-class'],
            purpose: (int)$params['payment-purpose'],
            comment: $params['payment-comment'] ?? '',
        );

        //Проверка на delivery, если платеж за доставку и delivery->cost не установлен
        if ($payment->purpose == PaymentOrder::PAY_DELIVERY && $order->delivery->cost == 0) {
            $order->delivery->cost = $payment->amount;
            $order->delivery->save();
        }
        $order->payments()->save($payment);
    }

    public function delPayment(PaymentOrder $payment)
    {
        $order = $payment->order;
        $payment->delete();

        $pays = PaymentOrder::where('order_id', $order->id)->where('purpose', PaymentOrder::PAY_ORDER)->getModels();
        if (empty($pays)) {
            flash('У заказа нет ни одного платежа!', 'warning');
        } else {
            $sum = array_sum(array_map(function (PaymentOrder $paymentOrder) {
                return $paymentOrder->amount;
            }, $pays));
            if ($order->total != $sum) flash('Сумма платежей не совпадает с заказом!', 'warning');
        }
    }

    public function paidPayment(PaymentOrder $payment, string $document, array $meta = [])
    {
        $order = $payment->order;
        $payment->document = $document;
        $payment->paid_at = now();
        $payment->meta = $meta;
        $payment->save();

        $allPaid = true;//Если все платежи оплачены, то Заказ => Оплачен!
        foreach ($order->payments as $paymentOrder) {
            if (!$paymentOrder->isPaid()) $allPaid = false;
        }
        if ($allPaid) $order->setPaid();
    }

    public function paidOrder(Order $order, string $document)
    {
        $order->setPaid();
        //TODO перенести в Order ??
        //Все неоплаченные платежи переводим в статус Оплачено.
        foreach ($order->payments as $paymentOrder) {
            if (!$paymentOrder->isPaid()) {
                $paymentOrder->document = $document;
                $paymentOrder->paid_at = now();
                $paymentOrder->save();
            }
        }
    }

    public function setStatus(Order $order, int $status)
    {
        $order->setStatus($status);
    }

    public function refund(Order $order, mixed $param)
    {
        //TODO Возврат денег!!!!
        // Алгоритм??
    }

    public function comleted(Order $order)
    {
        $order->setStatus(OrderStatus::COMPLETED);
        $order->finished = true;
        $order->save();
        $storage = $order->delivery->point;
        //Удаляем резерв
        foreach ($order->items as $item) {
            //TODO В хранилище уменьшить кол-во доступного товара на кол-во в резерв - ТЕСТ!!!!!
            $itemStorage = $storage->getItem($item->product);
            $itemStorage->quantity -= $item->reserve->quantity;
            $itemStorage->save();
            //$storage->getReserve();
            $item->reserve->delete();
        }
    }
}
