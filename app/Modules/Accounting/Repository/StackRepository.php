<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\DistributorProduct;
use App\Modules\Accounting\Entity\SupplyStack;

class StackRepository
{

    public function getByDistributor(Distributor $distributor): array
    {
        /** @var  */

        /** @var SupplyStack[] $stacks */
        $stacks = SupplyStack::where('supply_id', null)->getModels();

       foreach ($stacks as $i => $stack) {
            if (DistributorProduct::where('product_id', $stack->product->id)->get())
                if (!$distributor->isProduct($stack->product))
                    unset($stacks[$i]);
        }
     //   dd($stacks);
        $map = array_map(function (SupplyStack $stack) {
            return [
                'id' => $stack->id,
                'code' => $stack->product->code,
                'name' => $stack->product->name,
                'staff' => !is_null($stack->staff) ? $stack->staff->fullname->getFullName() : '-',
                'quantity' => $stack->quantity,
                'founded' => $stack->comment,
                'order_id' => !is_null($stack->orderItem) ? $stack->orderItem->order_id : null,
            ];
        },$stacks);
       $array = [];
       foreach ($map as $item) {
           $array[] = $item;
       }
       return $array;
    }
}
