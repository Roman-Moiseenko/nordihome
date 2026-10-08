<?php

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Domain\Interfaces\AttributeProductRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\Attribute;
use App\Modules\Catalog\Infrastructure\Models\AttributeProduct;
use App\Modules\Catalog\Infrastructure\Models\Product;

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

    public function getForProduct(int $productId): array
    {
        $product = Product::with('modification.prod_attributes')->findOrFail($productId);

        return $product->prod_attributes()
            ->with(['group', 'variants'])
            ->get()
            ->map(function (Attribute $attribute) use ($product) {
                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'group' => $attribute->group->name ?? '',
                    'value' => $attribute->Value(),
                    'variants' => $attribute->variants
                        ->map(fn($variant) => ['id' => $variant->id, 'name' => $variant->name])
                        ->values()
                        ->toArray(),
                    'is_variant' => $attribute->isVariant(),
                    'is_bool' => $attribute->isBool(),
                    'is_numeric' => $attribute->isNumeric(),
                    'is_date' => $attribute->isDate(),
                    'is_string' => $attribute->isString(),
                    'multiple' => (bool) $attribute->multiple,
                    'is_modification' => $product->AttributeIsModification($attribute->id),
                ];
            })
            ->values()
            ->toArray();
    }

    public function getPossibleForProduct(int $productId): array
    {
        $product = Product::findOrFail($productId);

        return array_values(array_filter(array_map(
            function (Attribute $attribute) use ($product) {
                if ($product->Value($attribute->id) !== null) {
                    return null;
                }

                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                ];
            },
            $product->getPossibleAttribute(),
        )));
    }

    public function syncProductAttributes(int $productId, array $attributes): void
    {
        $product = Product::findOrFail($productId);

        // Атрибуты, участвующие в модификации, отвязывать нельзя.
        $noDetach = [];
        if (!is_null($product->modification)) {
            foreach ($product->modification->prod_attributes as $attribute) {
                $noDetach[] = $attribute->id;
            }
        }

        if ($product->modification !== null) {
            foreach ($product->prod_attributes as $attribute) {
                if (!in_array($attribute->id, $noDetach, true)) {
                    $product->prod_attributes()->detach($attribute->id);
                }
            }
        } else {
            $product->prod_attributes()->detach();
        }

        foreach ($attributes as $item) {
            $attributeId = (int) $item['id'];
            if (in_array($attributeId, $noDetach, true)) {
                continue;
            }

            $attribute = Attribute::find($attributeId);
            if ($attribute === null) {
                continue;
            }

            $value = $this->resolveValue($attribute, $item['value'] ?? null);

            $product->prod_attributes()->attach($attribute->id, ['value' => json_encode($value)]);
        }
    }

    private function resolveValue(Attribute $attribute, mixed $value): mixed
    {
        if ($value === null) {
            if ($attribute->isBool()) {
                return false;
            }
            if ($attribute->isNumeric()) {
                return 0;
            }
            if ($attribute->isString()) {
                return '';
            }
            return null;
        }

        if ($attribute->isNumeric()) {
            return (float) $value;
        }

        return $value;
    }
}
