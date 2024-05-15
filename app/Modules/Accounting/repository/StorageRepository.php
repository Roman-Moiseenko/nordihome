<?php
declare(strict_types=1);

namespace App\Modules\Accounting\repository;

use App\Modules\Accounting\Entity\Storage;

class StorageRepository
{
    /**
     * Список хранилищ, где выдают товар
     * @return Storage[]
     */
    public function getPointDelivery(): array
    {
        return Storage::where('point_of_delivery', true)->getModel();
    }

    /**
     * Список хранилищ, где продают товар
     * @return Storage[]
     */
    public function getPointSale(): array
    {
        return Storage::where('point_of_sale', true)->getModel();
    }
}
