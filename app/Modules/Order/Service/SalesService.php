<?php


namespace App\Modules\Order\Service;


use App\Entity\Admin;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderResponsible;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Shop\Calculate\CalculatorOrder;
use App\Modules\Shop\Cart\Cart;
use App\Modules\Shop\Cart\CartItem;

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
    }

    public function setQuantity(Order $order, array $items)
    {
        $result = false;
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
            $cartItems[] = CartItem::create($item->product, $item->quantity, [], false);
        }
        $calculator = new CalculatorOrder();
        $cartItems = $calculator->calculate($cartItems);
        //TODO пересчет Заказа
        foreach ($cartItems as $cartItem) {
            //Заменить скидку в товарах
            //Посчитать общую сумму и общую скидку
        }
        return true;
        //Внести данные в Order
    }

    public function toOrder(Order $order)
    {
        //TODO Поменять статус
        $order->setStatus(OrderStatus::AWAITING);
        //Сформировать заявку на оплату

        // Отправить письмо клиенту


    }
}
