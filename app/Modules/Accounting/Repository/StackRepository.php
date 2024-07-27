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
        return $stacks;
    }
}
