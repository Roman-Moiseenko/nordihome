<?php

declare(strict_types=1);

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use App\Modules\Shop\Application\DTOs\Elements\ChildrenData;
use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomMainData;
use Illuminate\Support\Facades\DB;

class CategoryPageQueryRepository
{
    private const string PHOTO_MODEL_TYPE = 'catalog.product';

    public function __construct(
        private readonly PhotoService $photoService,
        private readonly AttributeQueryRepository $attributeQueryRepository,
    )
    {
    }

    public function getCategory(string $slug): ?CategoryRoomMainData
    {
        $row = DB::table('categories')
            ->leftJoin('categories as parent', 'parent.id', '=', 'categories.parent_id')
            ->where('categories.slug', $slug)
            ->select(
                'categories.id',
                'categories.name', 'categories.slug',
                'categories.meta',
                'categories.parent_id',
                'parent.name as parent_name',
                'parent.slug as parent_slug')
            ->first();
        if (!$row) return null;

        $childrenRows = DB::table('categories')->where('parent_id', $row->id)->get(['id', 'name', 'slug']);
        $children = $childrenRows->map(fn($c) => new ChildrenData($c->id, $c->name, $c->slug))->all();

        return new CategoryRoomMainData(
            id: $row->id,
            name: $row->name,
            slug: $row->slug,
            children: $children,
            entity: 'category',
            parent: $row->parent_id ? new ChildrenData($row->parent_id, $row->parent_name, $row->parent_slug) : null,
            totalProducts: 0,
            title: $meta['title'] ?? $row->name,
            description: $meta['description'] ?? '',
        );
    }

    public function getProductIdsInCategory(int $categoryId): ?array
    {
        $cat = DB::table('categories')
            ->where('id', $categoryId)
            ->select(['_lft', '_rgt'])
            ->first();

        if (!$cat) return null;

        return DB::table('products')
            ->where('products.published', true)
            ->where('products.not_sale', false)
            ->where(function ($q) use ($cat) {
                $q->whereExists(function ($sq) use ($cat) {
                    $sq->select(DB::raw(1))
                        ->from('categories')
                        ->whereColumn('categories.id', 'products.main_category_id')
                        ->where('categories._lft', '>=', $cat->_lft)
                        ->where('categories._rgt', '<=', $cat->_rgt);
                })
                    ->orWhereExists(function ($sq) use ($cat) {
                        $sq->select(DB::raw(1))
                            ->from('categories_products')
                            ->whereColumn('categories_products.product_id', 'products.id')
                            ->join('categories', 'categories.id', '=', 'categories_products.category_id')
                            ->where('categories._lft', '>=', $cat->_lft)
                            ->where('categories._rgt', '<=', $cat->_rgt);
                    });
            })
            ->pluck('products.id')->toArray();
    }

    public function getNewProductIds()
    {
        return Product::where('published', true)
            ->where('not_sale', false)
            ->where('published_at', '>', now()->subMonths(2))->pluck('id')->toArray();
    }

    /**
     * @param int[] $productIds
     * @return \stdClass[]
     */
    public function getRoomsByProductIds(array $productIds, array $filters): array
    {

        $query = Product::whereIn('id', $productIds);

        $this->attributeQueryRepository->applyFilters($query, $filters);
        $productIds = $query->pluck('products.id')->toArray();

        if (empty($productIds)) return [];

        return DB::table('rooms_products')
            ->join('rooms as child_rooms', 'rooms_products.room_id', '=', 'child_rooms.id')
            ->join('rooms as root_rooms', function ($join) {
                $join->whereNull('root_rooms.parent_id')
                    ->whereColumn('root_rooms._lft', '<=', 'child_rooms._lft')
                    ->whereColumn('root_rooms._rgt', '>=', 'child_rooms._rgt');
            })
            ->whereIn('rooms_products.product_id', $productIds)
            ->select('root_rooms.id', 'root_rooms.name', 'root_rooms.slug')
            ->distinct()
            ->orderBy('root_rooms.name')
            ->get()
            ->toArray();
    }

    public function getFilterAggregates(int $categoryId, array $productIds): object
    {
        $cat = DB::table('categories')
            ->where('id', $categoryId)
            ->select(['_lft', '_rgt'])
            ->first();

        if (!$cat) {
            return (object)[
                'min_price' => 0,
                'max_price' => 0,
                'brands' => [],
                'tags' => [],
                'attributes' => [],
            ];
        }

        if (empty($productIds)) {
            return (object)[
                'min_price' => 0,
                'max_price' => 0,
                'brands' => [],
                'tags' => [],
                'attributes' => [],
            ];
        }

        $categoryIds = DB::table('categories')
            ->where('_lft', '<=', $cat->_lft)->where('_rgt', '>=', $cat->_rgt)
            ->orWhere(function ($q) use ($cat) {
                $q->where('_lft', '>=', $cat->_lft)->where('_rgt', '<=', $cat->_rgt);
            })
            ->pluck('id')
            ->toArray();

        return $this->attributeQueryRepository->getFilterAggregatesByCategoryIdsAndProductIds(
            $categoryIds,
            $productIds
        );
    }

}
