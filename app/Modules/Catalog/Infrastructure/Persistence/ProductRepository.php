<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\DTOs\Product\ProductCategoryData;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Entity\Product;
use App\Modules\Catalog\Infrastructure\Models\CategoryProduct;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
public function findByMainCategoryId(int $categoryId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $query = Product::orderBy('name')
            ->where('main_category_id', $categoryId)
            ->where(function ($query) {
                $query->doesntHave('modification')->orHas('main_modification');
            });

        return $query->paginate($perPage, ['*'], 'page', $page)
            ->through(fn(Product $product) => new ProductCategoryData(
                id: $product->id,
                code: $product->code,
                name: $product->name,
                image: $product->miniImage(),
                published: (bool) $product->published,
                not_sale: (bool) $product->not_sale,
            ));
    }

    public function findAllByCategoryId(int $categoryId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        // ID товаров, привязанных через pivot
        $pivotProductIds = CategoryProduct::where('category_id', $categoryId)
            ->pluck('product_id')
            ->toArray();

        $query = Product::orderBy('name')
            ->where(function ($query) use ($categoryId, $pivotProductIds) {
                // Основная категория
                $query->where('main_category_id', $categoryId);

                // Или привязанные через pivot
                if (!empty($pivotProductIds)) {
                    $query->orWhereIn('id', $pivotProductIds);
                }
            })
            ->where(function ($query) {
                $query->doesntHave('modification')->orHas('main_modification');
            });

        return $query->paginate($perPage, ['*'], 'page', $page)
            ->through(fn(Product $product) => new ProductCategoryData(
                id: $product->id,
                code: $product->code,
                name: $product->name,
                image: $product->miniImage(),
                published: (bool) $product->published,
                not_sale: (bool) $product->not_sale,
            ));
    }

}
