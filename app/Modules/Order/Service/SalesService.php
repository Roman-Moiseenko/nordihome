<?php


namespace App\Modules\Order\Service;


use App\Entity\Admin;
use App\Events\MovementHasCreated;
use App\Events\OrderHasCanceled;
use App\Events\PointHasEstablished;
use App\Events\ThrowableHasAppeared;
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
        //TODO  Оповещение клиента, об увеличении времени резерва event(new OrderHasReserved($order)); ????
    }

    public function setQuantity(Order $order, array $items): bool
    {
        $result = false;
        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $new_quantity = (int)$item['quantity'];
                /** @var OrderItem $orderItem */
                $orderItem = OrderItem::find((int)$item['id']);
                //Если кол-во изменилось
                if ($orderItem->quantity != $new_quantity) {
                    $result = true; //Хотя бы одно изменение кол-ва
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
            DB::commit();
            //TODO  Оповещение клиента после отправки счета, об изменении кол-ва товаров event(new OrderQuantityHasChanged($order));
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            event(new ThrowableHasAppeared($e));
            return false;
            //return [$e->getMessage(), $e->getLine(), $e->getFile()];
        }
    }

    public function toOrder(Order $order)
    {
        //TODO Поменять статус
        $order->setStatus(OrderStatus::AWAITING);
        //Сформировать заявку на оплату
        // готовим документ для оплаты, по типу оплаты клиента ()


        // Отправить письмо клиенту
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

        $payment = PaymentOrder::new($cost, $userPayment->class_payment, '', PaymentOrder::PAY_DELIVERY);
        $order->payments()->save($payment);

    }

    public function setLogger(Order $order, int $logger_id)
    {
        $logger = Admin::find($logger_id);
        $order->responsible()->save(OrderResponsible::registerLogger($logger->id));
    }

    /**
     * Устанавливаем точку сборки заказа и при необходимости формируем заявку на перемещение товара
     * @param Order $order
     * @param int $storage_id
     * @return void
     */
    public function setMoving(Order $order, int $storage_id)
    {
        DB::beginTransaction();
        try {
            $storage = Storage::find($storage_id);
            $order->setPoint($storage->id); // Установить точку выдачи товара
            $order->setStorage($storage->id); // в резервах указать склад,
            $movements = $this->movements->createByOrder($order); //Создаем перемещения, если нехватает товара
            DB::commit();
            event(new PointHasEstablished($order));
            if (!is_null($movements)) event(new MovementHasCreated($movements));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            DB::rollBack();
            return;
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            //flash($e->getMessage(), 'danger');
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
            DB::rollBack();
        }
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
}
