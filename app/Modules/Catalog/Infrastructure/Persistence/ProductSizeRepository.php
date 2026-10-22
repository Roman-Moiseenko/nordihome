<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Accounting\Infrastructure\Models\Stock;
use App\Modules\Catalog\Domain\Entities\ProductSizeEntity;
use App\Modules\Catalog\Domain\Entities\SizeGridEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductSizeRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Catalog\Infrastructure\Models\ProductSize;

class ProductSizeRepository implements ProductSizeRepositoryInterface
{
    public function getByProductId(int $productId): array
    {
        return ProductSize::where('product_id', $productId)
            ->orderBy('id')
            ->get()
            ->map(fn(ProductSize $model) => $this->hydrate($model))
            ->all();
    }

    public function getProductIdsBySizeGridId(int $sizeGridId): array
    {
        return Product::where('size_grid_id', $sizeGridId)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();
    }

    public function rebuildForProduct(int $productId, SizeGridEntity $grid, array $desiredActive = []): void
    {
        $combinations = $grid->getCombinations();

        $existing = ProductSize::where('product_id', $productId)->get();
        $existingByKey = [];
        foreach ($existing as $model) {
            $existingByKey[ProductSizeEntity::keyFromValues($model->values ?? [])] = $model;
        }

        $validKeys = [];

        foreach ($combinations as $values) {
            $key = ProductSizeEntity::keyFromValues($values);
            $validKeys[] = $key;

            $model = $existingByKey[$key] ?? new ProductSize();

            $isActive = array_key_exists($key, $desiredActive)
                ? (bool) $desiredActive[$key]
                : (bool) ($existingByKey[$key]?->is_active ?? false);

            $model->product_id = $productId;
            $model->values = $values;
            $model->is_active = $isActive;
            $model->save();
        }

        $toDeleteIds = $existing
            ->filter(fn(ProductSize $model) => !in_array(
                ProductSizeEntity::keyFromValues($model->values ?? []),
                $validKeys,
                true,
            ))
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        if (!empty($toDeleteIds)) {
            $this->unlinkStock($toDeleteIds);
            ProductSize::whereIn('id', $toDeleteIds)->delete();
        }
    }

    public function clearForProduct(int $productId): void
    {
        $ids = ProductSize::where('product_id', $productId)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        if (!empty($ids)) {
            $this->unlinkStock($ids);
            ProductSize::whereIn('id', $ids)->delete();
        }
    }

    private function hydrate(ProductSize $model): ProductSizeEntity
    {
        $entity = new ProductSizeEntity(
            productId: $model->product_id,
            values: is_array($model->values) ? $model->values : [],
            isActive: (bool) $model->is_active,
        );
        $entity->id = $model->id;

        return $entity;
    }

    /**
     * Обнуляет связь stock с удаляемыми вариантами размеров.
     *
     * @param int[] $productSizeIds
     */
    private function unlinkStock(array $productSizeIds): void
    {
        Stock::whereIn('products_sizes_id', $productSizeIds)
            ->update(['products_sizes_id' => null]);
    }
}
