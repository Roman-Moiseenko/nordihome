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

    public function getNumericAttribute(Attribute $attribute, array $product_ids): array
    {
        $attr = array_map(function ($item) {
            return json_decode($item);
        }, AttributeProduct::where('attribute_id', '=', $attribute->id)->whereIn('product_id', $product_ids)->pluck('value')->toArray());


        return [
            'id' => $attribute->id,
            'name' => $attribute->name,
            'isNumeric' => true,
            'min' => min($attr),
            'max' => max($attr)
        ];


        //return $result;
    }

    public function getVariantAttribute(Attribute $attribute, array $product_ids)
    {
        $values = array_map(function ($item) {
            return json_decode($item);
        }, AttributeProduct::where('attribute_id', '=', $attribute->id)->whereIn('product_id', $product_ids)->pluck('value')->toArray());

        $variant_ids = [];
        foreach ($values as $item) {
            $variant_ids = array_merge($variant_ids, $item);
        }
        $variant_ids = array_unique($variant_ids);

        $variants = array_map(function ($id) {
            $_var = AttributeVariant::find($id);
            return [
                'id' => $_var->id,
                'name' => $_var->name,
                'image' => empty($_var->image->file) ? '' : $_var->getImage(),
            ];
        }, $variant_ids);

        $result = [
            'id' => $attribute->id,
            'name' => $attribute->name,
            'isVariant' => true,
            'variants' => $variants
        ];

        return $result;
    }

}
