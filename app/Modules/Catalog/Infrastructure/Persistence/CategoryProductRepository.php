<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\Interfaces\CategoryProductRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\CategoryProduct;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryProductRepository implements CategoryProductRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getProductIdsByCategoryId(int $categoryId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return CategoryProduct::where('category_id', $categoryId)
            ->select('product_id')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @inheritDoc
     */
    public function getCategoriesByProductId(int $productId): array
    {
        return CategoryProduct::where('product_id', $productId)
            ->pluck('category_id')
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function attachProducts(int $categoryId, array $productIds): void
    {
        $existing = CategoryProduct::where('category_id', $categoryId)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        $new = array_diff($productIds, $existing);

        foreach ($new as $productId) {
            $pivot = new CategoryProduct();
            $pivot->category_id = $categoryId;
            $pivot->product_id = $productId;
            $pivot->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function syncProducts(int $categoryId, array $productIds): void
    {
        CategoryProduct::where('category_id', $categoryId)->delete();

        foreach ($productIds as $productId) {
            $pivot = new CategoryProduct();
            $pivot->category_id = $categoryId;
            $pivot->product_id = $productId;
            $pivot->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function detachProducts(int $categoryId, array $productIds): void
    {
        CategoryProduct::where('category_id', $categoryId)
            ->whereIn('product_id', $productIds)
            ->delete();
    }

    /**
     * @inheritDoc
     */
    public function attachCategories(int $productId, array $categoryIds): void
    {
        $existing = CategoryProduct::where('product_id', $productId)
            ->whereIn('category_id', $categoryIds)
            ->pluck('category_id')
            ->toArray();

        $new = array_diff($categoryIds, $existing);

        foreach ($new as $categoryId) {
            $pivot = new CategoryProduct();
            $pivot->product_id = $productId;
            $pivot->category_id = $categoryId;
            $pivot->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function syncCategories(int $productId, array $categoryIds): void
    {
        CategoryProduct::where('product_id', $productId)->delete();

        foreach ($categoryIds as $categoryId) {
            $pivot = new CategoryProduct();
            $pivot->product_id = $productId;
            $pivot->category_id = $categoryId;
            $pivot->save();
        }
    }

    /**
     * @inheritDoc
     */
    public function detachCategories(int $productId, array $categoryIds): void
    {
        CategoryProduct::where('product_id', $productId)
            ->whereIn('category_id', $categoryIds)
            ->delete();
    }
}
