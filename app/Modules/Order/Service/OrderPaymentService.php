<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Analytics\LoggerService;
use App\Modules\Base\Entity\BankPayment;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpenseRefund;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Events\OrderHasPaid;
use App\Modules\Order\Events\OrderHasPrepaid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderPaymentService
{
    private LoggerService $logger;
    private float $commission_card;
    private float $commission_yookassa;

    public function __construct(
        LoggerService $logger,
    )
    {
        $this->logger = $logger;
        //TODO Из Настроек
        $this->commission_card = 2.0;
        $this->commission_yookassa = 2.7;
    }

    /**
     * Непривязанный платеж, когда не нашелся заказ
     */
    public function createUnresolved(int $shopper_id, int $trader_id, float $amount, int $method): OrderPayment
    {
        $payment = OrderPayment::new($amount, $method);
        $staff = Auth::guard('admin')->user();
        $payment->staff_id = $staff->id;
        $payment->shopper_id = $shopper_id;
        $payment->trader_id = $trader_id;
        $payment->save();

        return $payment;
    }

    /**
     * Создание платежа по банку. Автоматически проводится
     */
    public function create(Order $order, float $amount, int $method): OrderPayment
    {
        $payment = OrderPayment::new($amount, $method);
        $staff = Auth::guard('admin')->user();
        $payment->staff_id = $staff->id;
        $order->payments()->save($payment);

        $order->refresh();
        $this->checkPayment($order);
        $payment->completed();
        $this->logger->logOrder(order: $order, action: 'Внесена оплата',
            object: $payment->methodText(), value: price($payment->amount),
            link: route('admin.order.payment.show', $payment));

        return $payment;
    }

    /**
     * Назначение Заказа Платежу, для не распределенных.
     */
    public function setOrder(Order $order, OrderPayment $payment): void
    {
        DB::transaction(function () use ($order, $payment) {
            //Если сумма по платежу больше, то остаток переносится в новый нераспределенный платеж
            $debit = $order->getTotalAmount() - $order->getPaymentAmount();
            if ($debit < $payment->amount) {
                $new = $this->createUnresolved($payment->shopper_id, $payment->trader_id, $payment->amount - $debit, $payment->method);
                $new->bank_payment = $payment->bank_payment;
                $new->save();
                $payment->amount = $debit;
            }
            $payment->order_id = $order->id;
            $payment->completed = true;
            $payment->shopper_id = null;
            $payment->trader_id = null;
            $payment->save();
            $order->refresh();
            $this->checkPayment($order);
            $this->logger->logOrder(order: $order, action: 'Разнесена оплата',
                object: $payment->methodText(), value: price($payment->amount),
                link: route('admin.order.payment.show', $payment));
        });
    }

    /**
     * Создать платеж на основании из Заказа
     */
    public function createByOrder(Order $order, string $method): OrderPayment
    {
        $_method = null;
        if ($method == 'cash') $_method = OrderPayment::METHOD_CASH;
        if ($method == 'card') $_method = OrderPayment::METHOD_CARD;
        if ($method == 'account') $_method = OrderPayment::METHOD_ACCOUNT;
        if (is_null($_method)) throw new \DomainException('Неверный метод оплаты');

        $debt = $order->getTotalAmount() - $order->getPaymentAmount();
        $payment = OrderPayment::new($debt, $_method);
        $staff = Auth::guard('admin')->user();
        $payment->staff_id = $staff->id;
        $payment->manual = true;
        if ($payment->isCard()) $payment->commission = $this->commission_card;
        if ($payment->isYooKassa()) $payment->commission = $this->commission_yookassa;
        $order->payments()->save($payment);
        return $payment;
    }

    public function createYookassa(Order $order, string $id): OrderPayment
    {
        $payment = OrderPayment::new($order->getTotalAmount(), OrderPayment::METHOD_YOOKASSA);
        $staff = Auth::guard('admin')->user();
        $payment->staff_id = $staff->id;
        $payment->manual = false;
        $payment->commission = $this->commission_yookassa;
        $payment->yookassa_id = $id;
        $order->payments()->save($payment);
        return $payment;
    }


    public function setInfo(OrderPayment $payment, Request $request): void
    {
        if ($payment->completed) throw new \DomainException('Документ проведен');
        if (!$payment->manual) throw new \DomainException('Автоматический платеж, менять нельзя');

        if ($payment->method != $request->integer('method')) {
            $payment->method = $request->integer('method');
            if ($payment->isCard() && !$payment->isRefund()) { //Если картой и не возврат, тогда комиссия
                $payment->commission = $this->commission_card;
            } else {
                $payment->commission = 0.0;
            }
            $payment->save();
            return;
        }

        $payment->storage_id = $request->input('storage_id');
        $payment->amount = $request->integer('amount');
        $payment->commission = $request->float('commission');
        $payment->comment = $request->string('comment')->trim()->value();

        if ($payment->isAccount()) {
            $payment->bank_payment = BankPayment::fromArray($request->input('bank_payment'));
        } else {
            $payment->bank_payment = new BankPayment();
        }

        $payment->save();
    }

    public function destroy(OrderPayment $payment)
    {
        if (!$payment->isManual()) throw new \DomainException('Нельзя удалить загруженный платеж = ' . $payment->manual);
        if ($payment->isCompleted()) throw new \DomainException('Нельзя удалить проведенный документ');
        $payment->delete();
    }

    public function completed(OrderPayment $payment): void
    {
        if (is_null($payment->order_id)) throw new \DomainException('Нельзя провести платеж без привязки к договору');
        if ($payment->isCard() || $payment->isCash()) {
            if (is_null($payment->storage_id)) throw new \DomainException('Не выбрана точка расчета');
        }
        $payment->completed();
        $this->checkPayment($payment->order);
    }

    public function work(OrderPayment $payment): void
    {
        if (!$payment->manual) throw new \DomainException('Нельзя отменить проведение на автоматический платеж');
        $payment->work();

        $this->checkPayment($payment->order);
    }

    public function createByRefund(OrderExpenseRefund $refund): OrderPayment
    {
        $order = $refund->expense->order;
        $method = null; //Находим способ оплаты по последнему платежу к заказу
        foreach ($order->payments as $payment) {
            if (!$payment->isRefund()) {
                $method = $payment->method;
            }
        }
        if (is_null($method)) throw new \DomainException('Не найдены платежи по заказу');
        $payment = OrderPayment::new($refund->amount(), $method);

        $staff = Auth::guard('admin')->user();
        $payment->staff_id = $staff->id;
        $payment->is_refund = true; //Возврат
        $payment->manual = true; //В ручную
        $order->payments()->save($payment);
        $payment->refresh();

        $refund->order_payment_id = $payment->id;
        $refund->save();
        return $payment;
    }

    /**
     * Проверка Заказа после поступления/отмены оплаты, смена статуса, генерация события
     */
    private function checkPayment(Order $order): void
    {
        $payment = $order->getPaymentAmount();
        $total = $order->getTotalAmount();
        //Заказ в ожидании
        if ($order->status->value == OrderStatus::AWAITING) {
            $order->setReserve(now()->addDays(45));//Увеличиваем резерв
            if ($payment == $total) { //Оплачен полностью
                $order->setPaid();
                event(new OrderHasPaid($order));
            }
            if ($payment < $total) { //Оплачен частично
                $order->setStatus(OrderStatus::PREPAID);
                event(new OrderHasPrepaid($order));
            }
        }

        //Заказ на предоплате
        if ($order->status->value == OrderStatus::PREPAID) {
            if ($payment == $total) { //Оплачен полностью
                $order->setPaid();
                $order->setReserve(now()->addDays(45));//Увеличиваем резерв
                event(new OrderHasPaid($order));
            } else {
                if ($payment == 0) { //Отмена предоплаты
                    $order->delStatus(OrderStatus::PREPAID);
                } else { //Новая предоплата
                    $order->setReserve(now()->addDays(45));//Увеличиваем резерв
                    event(new OrderHasPrepaid($order));
                }

            }
        }
        if ($order->status->value == OrderStatus::PAID) {
            if ($payment == $total) throw new \DomainException('Неверный вызов checkPayment');
            if ($payment == 0) { //Полная отмена оплаты
                $order->delStatus(OrderStatus::PAID);
                $order->delStatus(OrderStatus::PREPAID);
            } else {
                $order->delStatus(OrderStatus::PAID);
                $order->refresh();
                if ($order->status->value != OrderStatus::PREPAID) $order->setStatus(OrderStatus::PREPAID);
            }
        }

        //Если купон в заказе, то завершаем его использование
        if (!is_null($order->coupon_id) && $order->coupon->isNew()) {
            $order->coupon->completed();
        }
    }
}
