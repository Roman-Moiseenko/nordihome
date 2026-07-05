<?php

declare(strict_types=1);

namespace App\Modules\Parser\Infrastructure\Persistence;

use App\Modules\Parser\Application\Interfaces\CategoryProductParserRepositoryInterface;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryProductParserRepository implements CategoryProductParserRepositoryInterface
{
    public function getProductIdsByCategoryId(int $categoryId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $category = ParserCategory::findOrFail($categoryId);

        return $category->products()
            ->select('parser_products.id')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getCategoriesByProductId(int $productId): array
    {
        $product = ParserProduct::findOrFail($productId);

        return $product->categories()
            ->pluck('parser_categories.id')
            ->toArray();
    }

    public function attachProducts(int $categoryId, array $productIds): void
    {
        $category = ParserCategory::findOrFail($categoryId);
        $category->products()->attach($productIds);
    }

    public function syncProducts(int $categoryId, array $productIds): void
    {
        $category = ParserCategory::findOrFail($categoryId);
        $category->products()->sync($productIds);
    }

    public function detachProducts(int $categoryId, array $productIds): void
    {
        $category = ParserCategory::findOrFail($categoryId);
        $category->products()->detach($productIds);
    }

    public function attachCategories(int $productId, array $categoryIds): void
    {
        $product = ParserProduct::findOrFail($productId);
        $product->categories()->attach($categoryIds);
    }

    public function syncCategories(int $productId, array $categoryIds): void
    {
        $product = ParserProduct::findOrFail($productId);
        $product->categories()->sync($categoryIds);
    }

    public function detachCategories(int $productId, array $categoryIds): void
    {
        $product = ParserProduct::findOrFail($productId);
        $product->categories()->detach($categoryIds);
    }
}
