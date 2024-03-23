<?php


namespace App\Modules\Order\Service;


use App\Entity\Admin;
use App\Events\MovementHasCreated;
use App\Events\OrderHasCanceled;
use App\Events\OrderHasLogger;
use App\Events\OrderHasRefund;
use App\Events\PointHasEstablished;
use App\Mail\OrderAwaiting;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderPaymentRefund;
use App\Modules\Order\Entity\Order\OrderResponsible;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\UserPayment;
use App\Modules\Shop\Calculate\CalculatorOrder;
use App\Modules\Shop\Cart\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SalesService
{

    private ReserveService $reserveService;
    private MovementService $movements;

    public function __construct(ReserveService $reserveService, MovementService $movements)
    {
        $this->reserveService = $reserveService;
        $this->movements = $movements;
    }

    public function setManager(Order $order, int $staff_id)
    {
        $staff = Admin::find($staff_id);
        if (empty($staff)) throw new \DomainException('Менеджер под ID ' . $staff_id . ' не существует!');
        $order->setStatus(OrderStatus::SET_MANAGER);
        $order->responsible()->save(OrderResponsible::registerManager($staff->id));
    }

    public function setReserveService(Order $order, string $date, string $time)
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
                $this->reserveService->subReserve($orderItem->reserve->id, $sub_reserve);
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
        if (!$order->isPoint()) throw new \DomainException('Не установлена точка сбора/выдачи!');

        //Проверить, если на доставку
        if (!$order->delivery->isStorage()) {
            if ($order->delivery->cost == 0) throw new \DomainException('Не установлена стоимость доставка');
            $payment_delivery = 0;
            foreach ($order->payments as $payment) {
                if ($payment->purpose == OrderAddition::PAY_DELIVERY) {
                    $payment_delivery += $payment->amount;
                }
            }
            if ($payment_delivery < $order->delivery->cost) throw new \DomainException('Сумма платежей по доставке меньше ее стоимости');
        }
        if (empty($order->payments)) throw new \DomainException('Нет ни одного платежа');

        //Проверить на сумму платежа за заказ
        $payment_order = 0;
        foreach ($order->payments as $payment) {
            if ($payment->purpose == OrderAddition::PAY_ORDER) {
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
        //TODO Переработать Возможно отменить
        /** @var UserPayment $userPayment */
        $userPayment = UserPayment::where('user_id', $order->user_id)->first();

        if ($order->delivery->cost != 0) {
            $pay = OrderAddition::where('order_id', $order->id)->where('amount', $order->delivery->cost)->first();
            if (!empty($pay)) $pay->delete();
        }

        $order->delivery->cost = $cost;
        $order->delivery->save();

        $payment = OrderAddition::new($cost, OrderAddition::PAY_DELIVERY);
        $order->additions()->save($payment);
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
        $order->setPoint($storage->id); //1. Установить точку выдачи товара
        $movements = $this->movements->createByOrder($order); //2. Создаем перемещения, если нехватает товара
        $order->setStorage($storage->id); //3. В резервах товаров установить склад.
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
            $this->reserveService->delete($item->reserve);
        }
        $order->setStatus(value: OrderStatus::CANCEL, comment: $comment);
        event(new OrderHasCanceled($order));
    }

    public function setPayment(Order $order, array $params)
    {
        $payment = OrderAddition::new(
            amount: (float)$params['payment-amount'],
            purpose: (int)$params['payment-purpose'],
            comment: $params['payment-comment'] ?? '',
        );

        //Проверка на delivery, если платеж за доставку и delivery->cost не установлен
        if ($payment->purpose == OrderAddition::PAY_DELIVERY && $order->delivery->cost == 0) {
            $order->delivery->cost = $payment->amount;
            $order->delivery->save();
        }
        $order->additions()->save($payment);
    }

    public function delPayment(OrderAddition $payment)
    {
        $order = $payment->order;
        $payment->delete();

        $pays = OrderAddition::where('order_id', $order->id)->where('purpose', OrderAddition::PAY_ORDER)->getModels();
        if (empty($pays)) {
            flash('У заказа нет ни одного платежа!', 'warning');
        } else {
            $sum = array_sum(array_map(function (OrderAddition $paymentOrder) {
                return $paymentOrder->amount;
            }, $pays));
            if ($order->total != $sum) flash('Сумма платежей не совпадает с заказом!', 'warning');
        }
    }

    public function paidPayment(OrderAddition $payment, string $document, array $meta = [])
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

    public function refund(Order $order, string $comment)
    {
        //TODO Возврат денег!!!!Алгоритм?? Тестить!
        $order->setStatus(OrderStatus::REFUND, $comment);
        //Возврат товаров в продажу
        foreach ($order->items as $item) {
            $this->reserveService->delete($item->reserve);
        }
        //Все платежи на возврат
        foreach ($order->payments as $payment) {
            //Платежный документ для возврата
            if (!$payment->isRefund()) {
                //Возможно для разных типов платежей разный способ расчета возврата денег
                $amount = $payment->amount;
                OrderPaymentRefund::register($payment->id, $amount, $comment);
                //$payment->setRefund();
            }
        }
        event(new OrderHasRefund($order)); //Оповещение менеджера по возврату денег
    }

    public function comleted(Order $order)
    {
        $order->setStatus(OrderStatus::COMPLETED);
        $order->finished = true;
        $order->save();
        $storage = $order->delivery->point;
        //Удаляем резерв
        foreach ($order->items as $item) {
            $itemStorage = $storage->getItem($item->product);
            $itemStorage->quantity -= $item->reserve->quantity;
            $itemStorage->save();
            $item->reserve->delete();
        }
    }

    public function createOrder(Request $request)
    {
        return null;
    }
}
