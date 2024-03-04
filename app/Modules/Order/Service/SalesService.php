<?php


namespace App\Modules\Order\Service;


use App\Entity\Admin;
use App\Events\ThrowableHasAppeared;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderResponsible;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Shop\Calculate\CalculatorOrder;
use App\Modules\Shop\Cart\Cart;
use App\Modules\Shop\Cart\CartItem;
use Illuminate\Support\Facades\DB;

class SalesService
{
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
                    $reserve = $orderItem->reserve->quantity;
                    $sub_reserve = $reserve - $new_quantity;
                    if ($sub_reserve > 0) {
                        $orderItem->reserve->update(
                            ['quantity' => $sub_reserve],
                        );
                        $orderItem->product->count_for_sell += $sub_reserve;
                        $orderItem->product->save();
                    }
                    $orderItem->changeQuantity($new_quantity);
                }
            }
            if (!$result) return false;
            $order->refresh();
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
            //TODO пересчет Заказа - Проверить
            $amount = 0;
            $total = 0;
            foreach ($cartItems as $cartItem) {
                $orderItem = $order->getItem($cartItem->product);
                $orderItem->discount_id = $cartItem->discount_id;
                $orderItem->discount_type = $cartItem->discount_type;
                $orderItem->sell_cost = $cartItem->discount_cost;
                $orderItem->save();
                $amount += $orderItem->quantity * $orderItem->base_cost;
                $total += $orderItem->quantity * $orderItem->sell_cost;
                //Заменить скидку в товарах
                //Посчитать общую сумму и общую скидку
            }
            $order->amount = $amount;
            $order->total = $total;
            $order->discount = $amount - $total - $order->coupon;
            $order->save();//Внести данные в Order
            DB::commit();
            //TODO  Оповещение клиента после отправки счета, об изменении кол-ва товаров event(new OrderQuantityHasChanged($order));
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            event(new ThrowableHasAppeared($e));
            return false;
        }
    }

    public function toOrder(Order $order)
    {
        //TODO Поменять статус
        $order->setStatus(OrderStatus::AWAITING);
        //Сформировать заявку на оплату

        // Отправить письмо клиенту


    }

    public function setDelivery(Order $order, float $cost)
    {
        //Установить стоимость доставки
    }

    public function setMoving(Order $order, int $storage_id)
    {
        //TODO Формируем не проведенную заявку на перемещение
        //Создаем event:
        //уведомляем ответственных за перемещение, руководство
        //Запись о действиях с заказом ????

    }

    public function destroy(Order $order)
    {
        if ($order->status->value == OrderStatus::FORMED) {
            $order->delete();
        } else {
            throw new \DomainException('Нельзя удалить заказ, который уже в работе');
        }

    }

    public function canceled(Order $order)
    {
        $order->setStatus(OrderStatus::CANCEL);
        //TODO Оповещаем Клиента об отмене Заказа
        event();
    }
}
