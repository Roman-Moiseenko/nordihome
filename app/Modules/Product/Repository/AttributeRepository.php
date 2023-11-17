<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeCategory;

class AttributeRepository
{

    public function existAndGet(int $id):? Attribute
    {
        try {
            return Attribute::findOrFail($id);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getPossibleForProduct(): array
    {

    }

    public function byName(string $name, int $category_id): Attribute
    {
        $attrs = AttributeCategory::where('category_id', '=', $category_id)->pluck('attribute_id')->toArray();

        return Attribute::where('name', '=', $name)->whereIn('id', $attrs)->first();
    }

}
