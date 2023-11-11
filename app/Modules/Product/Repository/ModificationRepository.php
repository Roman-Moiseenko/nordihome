<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Modification;
use App\Modules\Product\Entity\ModificationProduct;

class ModificationRepository
{

    //Массив всех товаров которые входят во все модификации
    public function getAllIdsArray()
    {
        $result = array_merge($this->getBaseIdsArray(), $this->getAssignmentIdsArray());
        return array_unique($result);
    }

    public function getAssignmentIdsArray()
    {
        return ModificationProduct::orderBy('product_id')->pluck('product_id')->toArray();
    }

    public function getBaseIdsArray()
    {
        return Modification::orderBy('id')->pluck('base_product_id')->toArray();
    }
}
