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

    #[Deprecated]
    public function create_original(Order $order)
    {

        foreach ($order->items as $item) {
            /** @var OrderIssuance $issuance */
            if (!empty($issuance = OrderIssuance::where('order_id', $order->id)->where('product_id', $item->product_id)->first())) {
                $issuance->value += $item->quantity;
                $issuance->save();
            } else {
                $issuance = OrderIssuance::new_product($item->product_id, $item->quantity, $item->comment);
                $order->issuances()->save($issuance);
            }
            if ($item->assemblage) {
                $assemblage = $item->product->assemblage ?? ($item->getSellCost() * $item->getQuantity() * $this->assemblage / 100);

                $issuance = OrderIssuance::new_service('Сборка товара ' . $item->product->name . ' ( ' . $item->quantity . ' шт.)', $assemblage, $item->comment);
                $order->issuances()->save($issuance);
            }
        }

        foreach ($order->additions as $addition) {
            $issuance = OrderIssuance::new_service($addition->purposeHTML(), $addition->amount, $addition->comment);
            $order->issuances()->save($issuance);
        }
    }

    public function create(Order $order, array $request): OrderExpense
    {
        $expense = OrderExpense::register($order->id);
        $products = $request['products'];
        foreach ($products as $product) {
            $expense->items()->save(OrderExpenseItem::new($product['id'], $product['quantity']));
        }
        $additions = $request['additions'];
        foreach ($additions as $addition) {
            $expense->additions()->save(OrderExpenseAddition::new($addition['id'], $addition['amount']));
        }
        $expense->refresh();
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

    public function setPoint(OrderExpense $expense, int $storage_id)
    {
        $storage = Storage::find($storage_id);
        $expense->setPoint($storage->id); //1. Установить точку выдачи товара
        //TODO Если нехватает создаем перемещение
        $movements = $this->movements->createByExpense($expense); //2. Создаем перемещения, если нехватает товара
        $expense->setStorage($storage->id); //3. В резервах товаров установить склад.
        //event(new PointHasEstablished($order));
        if (!is_null($movements)) event(new MovementHasCreated($movements));
    }

/*
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
