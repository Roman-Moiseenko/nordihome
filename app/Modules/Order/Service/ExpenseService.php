<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Events\MovementHasCreated;
use App\Events\PointHasEstablished;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Admin\Entity\Options;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderExpenseAddition;
use App\Modules\Order\Entity\Order\OrderExpenseItem;
use App\Modules\Order\Entity\Order\OrderIssuance;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\Deprecated;

class ExpenseService
{


    private MovementService $movements;

    public function __construct(MovementService $movements)
    {
        $this->assemblage = (new Options())->shop->assemblage ?? 15;
        $this->movements = $movements;
    }

    public function create(array $request): OrderExpense
    {
        /** @var Storage $storage */
        $storage = Storage::find($request['storage_id']);
        /** @var Order $order */
        $order = Order::find($request['order_id']);

        $to_movement = [];
        $expense = OrderExpense::register($order->id, $storage->id);
        $items = $request['items'];
        foreach ($items as $item) {
            /** @var OrderItem $orderItem */
            $orderItem = OrderItem::find($item['id']);
            $quantity = (int)$item['value'];

            $expense->items()->save(OrderExpenseItem::new($orderItem->id, $quantity));
            if (is_null($orderItem->reserve)) throw new \DomainException('Товара нет в резерве, дождитесь исполнения поступления!');
            $orderItem->reserve->sub($quantity);
            $storageItem = $storage->getItem($orderItem->product);
            $storageItem->sub($quantity);

            $storageItem->refresh();
            if ($storageItem->quantity < 0) {
                $to_movement[] = [
                    'product_id' => $storageItem->product_id,
                    'quantity' => -1 * $storageItem->quantity,
                ];
            }
        }
        $expense->storage_id = $storage->id;
        $expense->save();

        $additions = $request['additions'] ?? [];
        foreach ($additions as $addition) {
            $expense->additions()->save(OrderExpenseAddition::new((int)$addition['id'], (float)$addition['value']));
        }
        $expense->refresh();

        if (!empty($to_movement)) {
            //TODO Создаем перемещение или Оставляем, тогда перемещение в ручную
            // Событие event()!!
            throw new \DomainException('Требуется перемещение');
        }
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
            $item->orderItem->reserve->add($item->quantity);
            $storageItem = $expense->storage->getItem($item->orderItem->product);
            $storageItem->add($item->quantity);
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
