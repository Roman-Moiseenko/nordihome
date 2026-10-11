<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Application\Actions\ProductPrice\GetLatestPricesQuery;
use App\Modules\Accounting\Application\Actions\ProductPrice\GetPreviousPricesQuery;
use App\Modules\Accounting\Domain\ValueObjects\PriceType;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\BatchSale;
use App\Modules\Accounting\Entity\SurplusProduct;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderExpenseRefund;

class BatchSaleService
{
    public function __construct(
        private readonly GetLatestPricesQuery   $latestPricesQuery,
        private readonly GetPreviousPricesQuery $previousPricesQuery,
    )
    {
    }
    public function create(OrderExpense $expense): void
    {

        foreach ($expense->items as $item) {
            //TODO Добавить оприходование
            // Ищем излишки по товару, если есть то заполняем по наличию
            $arrival = false; //$product - SurplusProduct
            $products = [];

            if (is_null($item->orderItem->supply_stack_id)) {
                $products = SurplusProduct::where('product_id', $item->orderItem->product_id)
                    ->where('remains', '>', 0)
                    ->whereHas('document', function ($query) {
                        $query->where('completed');
                    })->getModels();
            }

            if (empty($products)) {
                $arrival = true; //$product - ArrivalProduct
                $query = ArrivalProduct::orderBy('id')
                    ->where('product_id', $item->orderItem->product_id)
                    ->where('remains', '>', 0)
                    ->whereHas('document', function ($query) {
                        $query->where('completed', true);
                    });

                if (!is_null($item->orderItem->supply_stack_id)) {
                    $ids = $item->orderItem->supplyStack->supply->arrivals()->get()->pluck('id')->toArray();
                    $query->whereIn('arrival_id', $ids);
                }
                $products = $query->getModels();
            }
            //dd([$item->orderItem->supply_stack_id, $products]);
            $quantity = (float)$item->quantity;

            /** @var ArrivalProduct|SurplusProduct $product */
            foreach ($products as $product) {
                if ($arrival) {
                    $prices = $this->latestPricesQuery->execute($item->orderItem->product_id);
                    $cost = $prices[PriceType::COST];
                } else {
                    $cost = $product->cost;
                }

                $batch_quantity = min($quantity, (float)$product->quantity);
                if ($arrival) {
                    $batch = BatchSale::register($item->id, $batch_quantity, $cost, $product->id, null);
                } else {
                    $batch = BatchSale::register($item->id, $batch_quantity, $cost, null, $product->id);
                }
                $batch->product_id = $item->orderItem->product_id;
                $batch->sell_cost = $item->orderItem->sell_cost;
                $batch->save();
                $product->batchSale($batch_quantity);
                $quantity -= $batch_quantity;
                if ($quantity == 0) break;
            }
        }
    }

    public function returnByExpense(OrderExpense $expense): void
    {

        $batches = BatchSale::whereHas('expenseItem', function ($query) use ($expense) {
            $query->where('expense_id', $expense->id);
        })->getModels();
        foreach ($batches as $batch) {
            //Возвращаем кол-во в Поступление
            if (is_null($batch->arrivalProduct)) {
                $batch->surplusProduct->remains += $batch->quantity;
                $batch->surplusProduct->save();
            } else {
                $batch->arrivalProduct->remains += $batch->quantity;
                $batch->arrivalProduct->save();
            }
            $batch->delete(); //Удаляем проведение
        }
    }

    public function returnByRefund(OrderExpenseRefund $refund): void
    {
        /** @var BatchSale[] $batches */
        $batches = BatchSale::whereHas('expenseItem', function ($query) use ($refund) {
            $query->where('expense_id', $refund->expense_id);
        })->getModels();

        foreach ($refund->items as $item) {
            $quantity = $item->quantity;

            foreach ($batches as $batch) {

                if ($item->expenseItem->orderItem->product_id == $batch->product_id
                    && $quantity > 0) {
                    $min = min($quantity, $batch->quantity);
                    //Возвращаем кол-во в Поступление
                    if (is_null($batch->arrivalProduct)) {
                        $batch->surplusProduct->remains += $min;
                        $batch->surplusProduct->save();
                    } else {
                        $batch->arrivalProduct->remains += $min;
                        $batch->arrivalProduct->save();
                    }
                    if ($batch->quantity == $min) {
                        $batch->delete(); //Удаляем проведение
                    } else {
                        $batch->quantity -= $min;
                        $batch->save();
                    }
                    $quantity -= $min;
                }
            }
        }
    }

}
