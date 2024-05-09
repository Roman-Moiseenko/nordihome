<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Events\ExpenseHasCompleted;
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


    private MovementService $movements;
    private int $assemblage;
    private OrderReserveService $reserveService;

    public function __construct(MovementService $movements, OrderReserveService $reserveService)
    {
        $this->assemblage = (new Options())->shop->assemblage ?? 15;
        $this->movements = $movements;
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
        $expense->storage_id = $storage->id;
        $expense->save();
        $additions = $request['additions'] ?? [];
        foreach ($additions as $addition) {
            $expense->additions()->save(OrderExpenseAddition::new((int)$addition['id'], (float)$addition['value']));
        }
        $expense->refresh();

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
     * ВЫдатьтовар со склада
     * @param array $request
     * @return OrderExpense
     */
    public function issue(array $request): OrderExpense
    {
        $expense = $this->create($request);

        $expense->type = OrderExpense::DELIVERY_STORAGE;
        $expense->recipient = clone $expense->order->user->fullname;
        $expense->phone = $expense->order->user->phone;
        $expense->save();
        $expense->setNumber();
        $this->completed($expense);
        $expense->refresh();
        return $expense;
    }

    public function assembly(OrderExpense $expense)
    {

    }

    public function completed(OrderExpense $expense)
    {
        $expense->completed();

        if ($expense->order->getExpenseAmount() == $expense->order->getTotalAmount()) {
            $expense->order->setStatus(OrderStatus::COMPLETED);
            event(new OrderHasCompleted($expense->order));
        } else {
            event(new ExpenseHasCompleted($expense));
        }
    }

    //Установить точку сборки
    /*
        public function setPoint(OrderExpense $expense, int $storage_id)
        {
            $storage = Storage::find($storage_id);
            $expense->setPoint($storage->id); //1. Установить точку выдачи товара
            $movements = $this->movements->createByExpense($expense); //2. Создаем перемещения, если нехватает товара
            $expense->setStorage($storage->id); //3. В резервах товаров установить склад.
            //event(new PointHasEstablished($order));
            if (!is_null($movements)) event(new MovementHasCreated($movements));
        }

        public function create_original(Order $order)
        {
            $expense = OrderExpense::register_original($order->id);

            foreach ($order->items as $item) {
                $expenseItem = OrderExpenseItem::new($item->product_id, $item->quantity);
                $expense->items()->save($expenseItem);
                if ($item->assemblage) {
                    $assemblage = $item->product->assemblage ?? ($item->getSellCost() * $item->getQuantity() * $this->assemblage / 100);

                    $expenseAddition = OrderExpenseAddition::new('Сборка товара ' . $item->product->name, $assemblage);
                    $expense->additions()->save($expenseAddition);
                }
            }

            foreach ($order->additions as $addition) {
                $expenseAddition = OrderExpenseAddition::new($addition->purposeHTML(), $addition->amount);
                $expense->additions()->save($expenseAddition);
            }

        }*/
}
