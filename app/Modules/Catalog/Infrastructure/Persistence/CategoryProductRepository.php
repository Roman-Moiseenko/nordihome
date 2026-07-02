<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\DTOs\Category\CategoryProductData;
use App\Modules\Catalog\Application\DTOs\Product\ProductCategoryData;
use App\Modules\Catalog\Application\Interfaces\CategoryProductRepositoryInterface;
use App\Modules\Catalog\Entity\Product;
use App\Modules\Catalog\Infrastructure\Models\CategoryProduct;
use App\Modules\Catalog\Infrastructure\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryProductRepository implements CategoryProductRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getProductsByCategoryId(int $categoryId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $productIds = CategoryProduct::where('category_id', $categoryId)
            ->pluck('product_id');

        return Product::orderBy('name')
            ->whereIn('id', $productIds)
            ->where(function ($query) {
                $query->doesntHave('modification')->orHas('main_modification');
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn(Product $product) => new ProductCategoryData(
                id: $product->id,
                code: $product->code,
                name: $product->name,
                image: $product->miniImage(),
                published: (bool) $product->published,
                not_sale: (bool) $product->not_sale,
            ));
    }

    /**
     * @inheritDoc
     */
    public function getCategoriesByProductId(int $productId): array
    {
        $categoryIds = CategoryProduct::where('product_id', $productId)
            ->pluck('category_id');

        return Category::whereIn('id', $categoryIds)
            ->orderBy('name')
            ->get()
            ->map(fn(Category $category) => new CategoryProductData(
                id: $category->id,
                name: $category->name,
                slug: $category->slug,
            ))
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
