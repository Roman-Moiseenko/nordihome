<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\BatchSale;
use App\Modules\Accounting\Entity\SurplusProduct;
use App\Modules\Order\Entity\Order\OrderExpense;

class BatchSaleService
{
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
                    $cost = $item->orderItem->product->getPriceCost();
                } else {
                    $cost = $product->cost;
                }

                $batch_quantity = min($quantity, (float)$product->quantity);
                if ($arrival) {
                    $batch = BatchSale::register($product->id, $item->order_item_id, $batch_quantity, $cost, null);
                } else {
                    $batch = BatchSale::register(null, $item->order_item_id, $batch_quantity, $cost, $product->id);
                }
                $batch->product_id = $item->orderItem->product_id;
                $batch->sell_cost = $item->orderItem->sell_cost;
                $batch->expense_id = $expense->id;
                $batch->save();
                $product->batchSale($batch_quantity);
                $quantity -= $batch_quantity;
                if ($quantity == 0) break;
            }
        }
    }

    public
    function return(OrderExpense $expense): void
    {
        /** @var BatchSale[] $batches */
        $batches = BatchSale::where('expense_id', $expense->id)->getModels();
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
}
