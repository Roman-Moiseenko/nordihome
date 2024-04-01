<?php
declare(strict_types=1);

namespace App\Modules\Product\Repository;

use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeCategory;
use App\Modules\Product\Entity\AttributeProduct;
use App\Modules\Product\Entity\AttributeVariant;

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

    public function byName(string $name, int $category_id): Attribute
    {
        $attrs = AttributeCategory::where('category_id', '=', $category_id)->pluck('attribute_id')->toArray();

        return Attribute::where('name', '=', $name)->whereIn('id', $attrs)->first();
    }

    public function getPossibleForCategory(array $parents_id)
    {
        return Attribute::whereHas('categories', function ($query) use ($parents_id) {
            $query->whereIn('category_id', $parents_id);
        })->get();
    }

    public function getIndex($category_id, $group_id)
    {
        $query = Attribute::orderBy('name');
        if (!empty($category_id) && $category_id != 0) {
            $query->whereHas('categories', function ($q) use ($category_id) {
                $q->where('id', '=', $category_id);
            });
        }
        if (!empty($group_id)) {
            $query->where('group_id', $group_id);
        }

        return $query;
    }


}
