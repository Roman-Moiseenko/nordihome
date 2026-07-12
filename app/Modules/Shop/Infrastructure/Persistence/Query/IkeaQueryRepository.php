<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use App\Modules\Shop\Application\DTOs\Entities\IkeaCategoryMainData;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class IkeaQueryRepository
{

    public function getCategoryBySlug(string $slug): IkeaCategoryMainData
    {
        $row = ParserCategory::where('slug', $slug)
            ->select('id', 'name', 'slug')
            ->firstOrFail();

        return new IkeaCategoryMainData(
            id: $row->id,
            name: $row->name,
            slug: $row->slug,
        );
    }

    /**
     * Список id всех товаров из всех вложенных категорий, категории $id
     * @param int $id
     * @return array
     */
    public function getProductIdsInCategory(int $id): array
    {
        $cat = DB::table('parser_categories')
            ->where('id', $id)
            ->select(['_lft', '_rgt'])
            ->first();

        if (!$cat) return [];

        return DB::table('parser_products')
            ->where('parser_products.availability', true)
            ->whereExists(function ($sq) use ($cat) {
                $sq->select(DB::raw(1))
                    ->from('parser_categories_products')
                    ->whereColumn('parser_categories_products.product_id', 'parser_products.id')
                    ->join('parser_categories', 'parser_categories.id', '=', 'parser_categories_products.category_id')
                    ->where('parser_categories._lft', '>=', $cat->_lft)
                    ->where('parser_categories._rgt', '<=', $cat->_rgt);
            })
            ->pluck('parser_products.id')->toArray();
    }

    public function getPaginationProducts(mixed $allProductIds, int $page, int $perPage): LengthAwarePaginator
    {
        if (empty($allProductIds)) {
            return new LengthAwarePaginator(
                items: collect([]),
                total: 0,
                perPage: $perPage,
                currentPage: $page,
                options: ['path' => request()->url(), 'query' => request()->query()],
            );
        }

        $query = ParserProduct::whereIn('id', $allProductIds);

        // Пагинируем через Eloquent
        $paginator = $query->paginate($perPage, ['id'], 'page', $page);

        // Возвращаем пагинатор только с ID товаров
        $products = $paginator->getCollection()->get();

        return new LengthAwarePaginator(
            items: collect($products),
            total: $paginator->total(),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => request()->url(),
                'query' => request()->query(),
            ],
        );
    }
}
