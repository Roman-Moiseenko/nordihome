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

    private int $assemblage;
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

        $additions = $request['additions'];
        foreach ($additions as $addition) {
            $expense->additions()->save(OrderExpenseAddition::new($addition['id'], $addition['value']));
        }
        $expense->refresh();

        if (!empty($to_movement)) {
            //TODO Создаем перемещение

            throw new \DomainException('Требуется перемещение');
        }

        //$storage

        //Если на складе < 0 , то => Формируем перемещение

        return $expense;
    }

    //*** Изменения в Распоряжении - value, add_item, del_item
    public function update_item(OrderExpenseItem $item, $quantity) {
        //TODO Проверка на превышение
        $item->quantity = $quantity;
        $item->save();
        //TODO проверка на сумму оплаты
    }
    public function update_addition(OrderExpenseAddition $addition, $amount) {
        //TODO Проверка на превышение
        $addition->amount = $amount;
        $addition->save();
        //TODO проверка на сумму оплаты
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
