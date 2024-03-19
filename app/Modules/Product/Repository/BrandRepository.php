<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Brand;


class BrandRepository
{

    public function getIndex()
    {
        return Brand::orderBy('name');
    }

    public function byName(string $name): Brand
    {
        return Brand::where('name', '=', $name)->first();
    }
}
