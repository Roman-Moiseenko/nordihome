<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Events\ExpenseHasAssembly;
use App\Events\ExpenseHasCompleted;
use App\Events\ExpenseHasDelivery;
use App\Events\OrderHasCanceled;
use App\Events\OrderHasCompleted;
use App\Modules\Accounting\Entity\MovementProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Admin\Entity\Options;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderExpenseAddition;
use App\Modules\Order\Entity\Order\OrderExpenseItem;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderStatus;


class ExpenseService
{
    private OrderReserveService $reserveService;

    public function __construct(OrderReserveService $reserveService)
    {
        $this->reserveService = $reserveService;
    }

    public function create(array $request): OrderExpense
    {
        /** @var Storage $storage */
        $storage = Storage::find($request['storage_id']); //Откуда выдать товар
        /** @var Order $order */
        $order = Order::find($request['order_id']);

        $expense = OrderExpense::register($order->id, $storage->id);
        $items = $request['items'];
        foreach ($items as $item) {
            /** @var OrderItem $orderItem */
            $orderItem = OrderItem::find($item['id']);
            $quantity = (int)$item['value']; //Сколько выдать товара
            if ($quantity > 0) {
                $expense->items()->save(OrderExpenseItem::new($orderItem->id, $quantity));
                //Проверка на наличие на складе выдачи
                $reserve = $orderItem->getReserveByStorage($storage->id);
                if (is_null($reserve) || $reserve->quantity < $quantity)
                    throw new \DomainException('Товара нет в резерве на складе отгрузки, дождитесь исполнения поступления или перемещения!');
                $this->reserveService->downReserve($orderItem, $quantity); //Убираем из резерва
                $storageItem = $storage->getItem($orderItem->product);
                $storageItem->sub($quantity); //Списываем со склада

                $storageItem->refresh();
                if ($storageItem->quantity < 0) {
                    throw new \DomainException('Исключительная ситуация. Товар есть в резерве, НО требуется перемещение');
                }
            }
        }
        $expense->storage_id = $storage->id;
        $expense->save();
        $additions = $request['additions'] ?? [];
        foreach ($additions as $addition) {
            if ((float)$addition['value'] > 0)
                $expense->additions()->save(OrderExpenseAddition::new((int)$addition['id'], (float)$addition['value']));
        }
        $expense->refresh();

        return $expense;
    }

    public function create_expense(array $request): OrderExpense
    {
        $expense = $this->create($request);
        $user = $expense->order->user;
        $expense->recipient = clone $user->fullname;
        $expense->address = clone $user->address;
        $expense->phone = $user->phone;
        $expense->type = $user->delivery;
        $expense->save();

        return $expense;
    }

    /**
     * Отмена распоряжения. Возвращаем кол-во в резерв и на склад. Удаляем записи и само распоряжение
     * @param OrderExpense $expense
     * @return Order
     */
    public function cancel(OrderExpense $expense): Order
    {
        $order = $expense->order;
        foreach ($expense->items as $item) {

            $expense->storage->add($item->orderItem->product, $item->quantity);
            $expense->storage->refresh();
            $this->reserveService->upReserve($item->orderItem, $item->quantity);

            $item->delete();
        }
        $expense->delete();
        $order->refresh();
        return $order;
    }

    public function update_comment(OrderExpense $expense, string $comment)
    {
        $expense->comment = $comment;
        $expense->save();
    }

    /**
     * Регистрация выдачи товара на месте (без доставки)
     * @param array $request
     * @return OrderExpense
     */
    private function issue(array $request): OrderExpense
    {
        $expense = $this->create($request);

        $expense->type = OrderExpense::DELIVERY_STORAGE;
        $expense->recipient = clone $expense->order->user->fullname;
        $expense->phone = $expense->order->user->phone;
        $expense->save();
        $expense->setNumber();
        return $expense;
    }

    /**
     * Выдать товар из магазина, витрины
     * @param array $request
     * @return OrderExpense
     */
    public function issue_shop(array $request): OrderExpense
    {
        $expense = $this->issue($request);
        $this->completed($expense);
        $expense->refresh();
        return $expense;
    }

    /**
     * Выдать товар со склада
     * @param array $request
     * @return OrderExpense
     */
    public function issue_warehouse(array $request): OrderExpense
    {
        $expense = $this->issue($request);
        $this->assembly($expense);
        $expense->refresh();
        return $expense;
    }

    public function assembly(OrderExpense $expense): Order
    {
        if ($expense->isLocal() == false && $expense->isRegion() == false) throw new \DomainException('Не выбран тип доставки');
        if ($expense->isLocal() && is_null($expense->calendar())) throw new \DomainException('Не выбрано время доставки');
        if (empty($expense->phone) || empty($expense->address->address)) throw new \DomainException('Не указан адрес и/или телефон');
        $expense->setNumber();
        $expense->assembly();
        event(new ExpenseHasAssembly($expense)); //Уведомление на склад на выдачу
        return $expense->order;
    }

    public function assembling(OrderExpense $expense, int $worker_id)
    {
        $expense->worker_id = $worker_id;
        $expense->status = OrderExpense::STATUS_ASSEMBLING;
        $expense->save();
    }

    public function delivery(OrderExpense $expense, string $track = '')
    {
        if ($expense->isRegion()) {
            if (empty($track)) throw new \DomainException('Не указан трек-номер посылки');
            $expense->track = $track;
            event(new ExpenseHasDelivery($expense)); //Уведомляем клиента с трек-номером

        }
        $expense->status = OrderExpense::STATUS_DELIVERY;
        $expense->save();
    }

    public function completed(OrderExpense $expense)
    {
        $expense->completed();
        $expense->refresh();
        /** @var Order $order */
        $order = Order::find($expense->order_id);

        if (($order->getTotalAmount() - $order->getExpenseAmount() + $order->getCoupon() + $order->getDiscountOrder()) < 1) {
            $check = true;
            foreach ($order->expenses as $_expense) {
                if (!$_expense->isCompleted()) $check = false;
            }
            //Проверить все ли распоряжения выданы?
            if ($check) $expense->order->setStatus(OrderStatus::COMPLETED);

            event(new OrderHasCompleted($expense->order));
        } else {
            event(new ExpenseHasCompleted($expense));
        }
    }
}
