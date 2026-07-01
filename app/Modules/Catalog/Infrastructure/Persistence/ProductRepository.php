<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\DTOs\Product\ProductCategoryData;
use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Entity\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function findByCategoryId(int $categoryId, int $perPage = 15, int $page = 1): LengthAwarePaginator
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
}
