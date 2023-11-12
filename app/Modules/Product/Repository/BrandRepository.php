<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Brand;


class BrandRepository
{

    public function getIndex(int $pagination = null)
    {
        if (is_null($pagination)) {
            return Brand::orderBy('name');
        } else {
            return Brand::orderBy('name')->paginate($pagination);
        }

    }

    public function byName(string $name): Brand
    {
        return Brand::where('name', '=', $name)->first();
    }
}
