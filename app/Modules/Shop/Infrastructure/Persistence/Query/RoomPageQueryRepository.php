<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shared\Infrastructure\Services\PhotoService;
use App\Modules\Shop\Application\DTOs\Elements\ChildrenData;
use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomMainData;
use Illuminate\Support\Facades\DB;

class RoomPageQueryRepository
{
    private const string PHOTO_MODEL_TYPE = 'catalog.product';

    public function __construct(
        private readonly PhotoService $photoService,
    )
    {
    }

    public function getRoom(string $slug): ?CategoryRoomMainData
    {
        $row = DB::table('rooms')
            ->leftJoin('rooms as parent', 'parent.id', '=', 'rooms.parent_id')
            ->where('rooms.slug', $slug)
            ->select('rooms.id',
                'rooms.name', 'rooms.slug',
                'rooms.meta', 'rooms.parent_id',
                'parent.name as parent_name', 'parent.slug as parent_slug')
            ->first();
        if (!$row) return null;

        $childrenRows = DB::table('rooms')->where('parent_id', $row->id)->get(['id', 'name', 'slug']);
        $children = $childrenRows->map(fn($c) => new ChildrenData($c->id, $c->name, $c->slug))->all();

        return new CategoryRoomMainData(
            id: $row->id,
            name: $row->name,
            slug: $row->slug,
            children: $children,
            entity: 'room',
            parent: $row->parent_id ? new ChildrenData($row->parent_id, $row->parent_name, $row->parent_slug) : null,
            totalProducts: 0,
            title: $meta['title'] ?? $row->name,
            description: $meta['description'] ?? '',
        );

    }

    public function getProductIdsInRoom(int $roomId): ?array
    {
        $room = DB::table('rooms')
            ->where('id', $roomId)
            ->select(['_lft', '_rgt'])
            ->first();

        if (!$room) return null;

        return DB::table('products')
            ->where('products.published', true)
            ->where('products.not_sale', false)
            ->where(function ($q) use ($room) {
                $q->whereExists(function ($sq) use ($room) {
                    $sq->select(DB::raw(1))
                        ->from('rooms_products')
                        ->whereColumn('rooms_products.product_id', 'products.id')
                        ->join('rooms', 'rooms.id', '=', 'rooms_products.room_id')
                        ->where('rooms._lft', '>=', $room->_lft)
                        ->where('rooms._rgt', '<=', $room->_rgt);
                });
            })
            ->pluck('products.id')->toArray();
    }

    public function getCategoriesByProductIds(mixed $allProductIds, array $params): array
    {
        if (empty($allProductIds)) {
            return [];
        }

        // Находим ID категорий, к которым напрямую привязаны товары
        // (через main_category_id и через categories_products pivot)
        $linkedCategoryIds = DB::table('categories')
            ->where(function ($q) use ($allProductIds) {
                $q->whereExists(function ($sq) use ($allProductIds) {
                    $sq->select(DB::raw(1))
                        ->from('products')
                        ->whereColumn('products.main_category_id', 'categories.id')
                        ->whereIn('products.id', $allProductIds);
                })
                ->orWhereExists(function ($sq) use ($allProductIds) {
                    $sq->select(DB::raw(1))
                        ->from('categories_products')
                        ->whereColumn('categories_products.category_id', 'categories.id')
                        ->whereIn('categories_products.product_id', $allProductIds);
                });
            })
            ->pluck('id')
            ->toArray();

        if (empty($linkedCategoryIds)) {
            return [];
        }

        // Находим рутовые категории (parent_id IS NULL),
        // внутри _lft/_rgt которых находятся привязанные категории
        $linkedRanges = DB::table('categories')
            ->whereIn('id', $linkedCategoryIds)
            ->select('_lft', '_rgt')
            ->get();

        if ($linkedRanges->isEmpty()) {
            return [];
        }

        return DB::table('categories')
            ->whereNull('parent_id')
            ->where(function ($q) use ($linkedRanges) {
                foreach ($linkedRanges as $range) {
                    $q->orWhere(function ($sq) use ($range) {
                        $sq->where('_lft', '<=', $range->_lft)
                           ->where('_rgt', '>=', $range->_rgt);
                    });
                }
            })
            ->select('id', 'name', 'slug')
            ->distinct()
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function getFilterAggregates(array $categoryIds, array $productIds)
    {
        $cat = DB::table('categories')
            ->whereIn('id', $categoryIds)
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
