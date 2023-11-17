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

    public function getPossibleForProducts(array $products_id): array
    {
        //$attr = Attribute::where
    }

    public function byName(string $name, int $category_id): Attribute
    {
        $attrs = AttributeCategory::where('category_id', '=', $category_id)->pluck('attribute_id')->toArray();

        return Attribute::where('name', '=', $name)->whereIn('id', $attrs)->first();
    }

    public function getPossibleForCategory(array $parents_id)
    {
        $attrs = Attribute::whereHas('categories', function ($query) use ($parents_id) {
            $query->whereIn('category_id', $parents_id);
        })->get();
        return $attrs;
    }

    //Для списка Атрибутов в Модуль Shop
    //TODO вынести в отдельный репозиторий (Fetch
    public function getIdPossibleForCategory(array $parents_id)
    {
        $attrs = Attribute::whereHas('categories', function ($query) use ($parents_id) {
            $query->whereIn('category_id', $parents_id);
        })->pluck('id')->toArray();
        return $attrs;
    }
    public function getIdPossibleForProducts(array $products_id): array
    {
        $attrs = Attribute::whereHas('products', function ($query) use ($products_id) {
            $query->whereIn('product_id', $products_id);
        })->pluck('id')->toArray();
        return $attrs;
    }

}
