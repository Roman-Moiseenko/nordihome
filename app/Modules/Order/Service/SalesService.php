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
use JetBrains\PhpStorm\Deprecated;

class SalesService
{

    private ReserveService $reserveService;
    private MovementService $movements;
    private ExpenseService $expenseService;

    public function __construct(
        ReserveService $reserveService,
        MovementService $movements,
        ExpenseService $expenseService
    )
    {
        $this->reserveService = $reserveService;
        $this->movements = $movements;
        $this->expenseService = $expenseService;
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
            if (!is_null($item->reserve))
                $item->reserve->update([
                    'reserve_at' => $new_reserve,
                ]);
        }
    }


    /**
     * Отправить заказ на оплату - резерв, присвоение номера заказу, счет, услуги по сборке
     * @param Order $order
     * @return void
     */
    public function setAwaiting(Order $order)
    {
        if ($order->status->value != OrderStatus::SET_MANAGER) throw new \DomainException('Нельзя отправить заказ на оплату. Не верный статус');
        //if ($order->getSellAmount() == 0)  throw new \DomainException('Нет товара или цена равна 0!');
        if ($order->getTotalAmount() == 0)  throw new \DomainException('Сумма заказа не может быть равно нулю');

        foreach ($order->items as $item) {
            if ($item->assemblage == true) {
                $addition = OrderAddition::new($item->getAssemblage(), OrderAddition::PAY_ASSEMBLY, $item->product->name);
                $order->additions()->save($addition);
                $item->assemblage = false;
                $item->save();
            }
        }
        $order->setReserve(now()->addDays(3));
        $order->setNumber();
        $order->setStatus(OrderStatus::AWAITING);
        $order->refresh();

        Mail::to($order->user->email)->queue(new OrderAwaiting($order));
        flash('Заказ успешно создан! Ему присвоен номер ' . $order->number, 'success');
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

    #[Deprecated]
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
    #[Deprecated]
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
            if (!is_null($item->reserve)) $this->reserveService->delete($item->reserve);
        }
        $order->setStatus(value: OrderStatus::CANCEL, comment: $comment);
        event(new OrderHasCanceled($order));
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
