<?php

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Domain\Interfaces\AttributeProductRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\AttributeProduct;

class AttributeProductRepository implements AttributeProductRepositoryInterface
{

    public function valueOf(int $productId, int $attributeId): mixed
    {
        $model = AttributeProduct::where('attribute_id', $attributeId)
            ->where('product_id', $productId)
            ->first();

        if ($model === null) {
            return null;
        }

        // value — JSON-колонка: [variant_id] для variant, скаляр для остальных.
        return json_decode($model->value, true);
    }
}
