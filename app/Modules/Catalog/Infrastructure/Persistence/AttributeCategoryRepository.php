<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Domain\Interfaces\AttributeCategoryRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\AttributeCategory;

class AttributeCategoryRepository implements AttributeCategoryRepositoryInterface
{
    public function getCategoryIdsByAttributeId(int $attributeId): array
    {
        return AttributeCategory::where('attribute_id', $attributeId)
            ->pluck('category_id')
            ->all();
    }

    public function getCategoryIdsByAttributeIds(array $attributeIds): array
    {
        if (empty($attributeIds)) {
            return [];
        }

        $rows = AttributeCategory::whereIn('attribute_id', $attributeIds)
            ->get(['attribute_id', 'category_id']);

        $map = [];
        foreach ($rows as $row) {
            $map[$row->attribute_id][] = $row->category_id;
        }

        return $map;
    }

    public function attachCategories(int $attributeId, array $categoryIds): void
    {
        $existing = AttributeCategory::where('attribute_id', $attributeId)
            ->whereIn('category_id', $categoryIds)
            ->pluck('category_id')
            ->all();

        $new = array_values(array_diff($categoryIds, $existing));

        foreach ($new as $categoryId) {
            $pivot = new AttributeCategory();
            $pivot->attribute_id = $attributeId;
            $pivot->category_id = $categoryId;
            $pivot->save();
        }
    }

    public function syncCategories(int $attributeId, array $categoryIds): void
    {
        AttributeCategory::where('attribute_id', $attributeId)->delete();

        foreach ($categoryIds as $categoryId) {
            $pivot = new AttributeCategory();
            $pivot->attribute_id = $attributeId;
            $pivot->category_id = $categoryId;
            $pivot->save();
        }
    }

    public function detachCategories(int $attributeId, array $categoryIds): void
    {
        AttributeCategory::where('attribute_id', $attributeId)
            ->whereIn('category_id', $categoryIds)
            ->delete();
    }
}
