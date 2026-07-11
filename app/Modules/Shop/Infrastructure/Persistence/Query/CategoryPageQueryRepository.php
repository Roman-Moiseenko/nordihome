<?php

declare(strict_types=1);

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use App\Modules\Shop\Application\DTOs\Parts\CategoryRoomMainData;
use App\Modules\Shop\Application\DTOs\Parts\ChildrenData;
use Illuminate\Pagination\LengthAwarePaginator;
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
            ->select('categories.id', 'categories.name', 'categories.slug', 'categories.meta', 'categories.parent_id',
                'parent.name as parent_name', 'parent.slug as parent_slug')
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

    public function getPaginationProducts(array $filters, $allIds, int $page, int $perPage): LengthAwarePaginator
    {
        //$productIdsInCategory = $this->getProductIdsInCategory($categoryId);

        if (empty($allIds)) {
            return new LengthAwarePaginator(
                items: collect([]),
                total: 0,
                perPage: $perPage,
                currentPage: $page,
                options: ['path' => request()->url(), 'query' => request()->query()],
            );
        }

        $query = Product::whereIn('id', $allIds);

        $this->attributeQueryRepository->applyFilters($query, $filters);

        $this->attributeQueryRepository->applySorting($query, $filters['order'] ?? '');


        // Пагинируем через Eloquent
        $paginator = $query->paginate($perPage, ['id'], 'page', $page);

        // Возвращаем пагинатор только с ID товаров
        $ids = $paginator->getCollection()->pluck('id')->toArray();

        return new LengthAwarePaginator(
            items: collect($ids),
            total: $paginator->total(),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => request()->url(),
                'query' => request()->query(),
            ],
        );
    }

    public function getNewProductIds(array $filters, int $page, int $perPage): LengthAwarePaginator
    {
        $query = Product::where('published', true)
            ->where('not_sale', false)
            ->where('published_at', '>', now()->subMonths(2));

        $this->attributeQueryRepository->applySorting($query, $filters['order'] ?? '');

        $paginator = $query->paginate($perPage, ['id'], 'page', $page);

        $ids = $paginator->getCollection()->pluck('id')->toArray();

        return new LengthAwarePaginator(
            items: collect($ids),
            total: $paginator->total(),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => request()->url(),
                'query' => request()->query(),
            ],
        );
    }

    public function loadProductCards(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $orderedIds = implode(',', array_map('intval', $ids));
        $products = DB::table('products')
            ->whereIn('products.id', $ids)
            ->orderByRaw("FIELD(products.id, $orderedIds)")
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('product_prices', function ($join) {
                $join->on('products.id', '=', 'product_prices.product_id')
                    ->where('product_prices.type', '=', PriceType::RETAIL)
                    ->whereRaw('product_prices.id = (
                        SELECT MAX(pp2.id) FROM product_prices pp2
                        WHERE pp2.product_id = products.id AND pp2.type = \'' . PriceType::RETAIL . '\'
                    )');
            })
            ->select(
                'products.id',
                'products.name',
                'products.slug',
                'products.code',
                'products.current_rating',
                'products.priority',
                'products.published_at',
                'products.price_reduced',
                'products.only_on_order',
                'products.pre_order',
                'products.not_sale',
                'brands.name as brand_name',
                'product_prices.amount as price',
                DB::raw("(SELECT id FROM photos WHERE imageable_id = products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_id"),
                DB::raw("(SELECT file FROM photos WHERE imageable_id = products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_file"),
                DB::raw("(SELECT thumb FROM photos WHERE imageable_id = products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_thumb"),
                DB::raw("(SELECT alt FROM photos WHERE imageable_id = products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_alt"),
                DB::raw("(SELECT title FROM photos WHERE imageable_id = products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_title"),
                DB::raw("(SELECT description FROM photos WHERE imageable_id = products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_description"),
                DB::raw("(SELECT id FROM photos WHERE imageable_id = products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 1 LIMIT 1) as photo2_id"),
                DB::raw("(SELECT file FROM photos WHERE imageable_id = products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 1 LIMIT 1) as photo2_file"),
                DB::raw("(SELECT thumb FROM photos WHERE imageable_id = products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 1 LIMIT 1) as photo2_thumb"),
                DB::raw("(SELECT alt FROM photos WHERE imageable_id = products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 1 LIMIT 1) as photo2_alt"),
                DB::raw("(SELECT title FROM photos WHERE imageable_id = products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 1 LIMIT 1) as photo2_title"),
                DB::raw("(SELECT description FROM photos WHERE imageable_id = products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 1 LIMIT 1) as photo2_description"),
            )
            ->get();

        $reviewsCount = DB::table('product_reviews')
            ->whereIn('product_reviews.product_id', $ids)
            ->where('product_reviews.status', \App\Modules\Catalog\Entity\Review::STATUS_PUBLISHED)
            ->selectRaw('product_id, COUNT(*) as total')
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $now = now();
        $promotions = DB::table('promotions_products')
            ->join('promotions', 'promotions_products.promotion_id', '=', 'promotions.id')
            ->whereIn('promotions_products.product_id', $ids)
            ->where('promotions.active', true)
            ->where('promotions.start_at', '<=', $now)
            ->where('promotions.finish_at', '>=', $now)
            ->select(
                'promotions_products.product_id',
                'promotions_products.price',
                'promotions.title',
            )
            ->get()
            ->keyBy('product_id');

        $quantities = DB::table('storage_items')
            ->whereIn('storage_items.product_id', $ids)
            ->selectRaw('product_id, SUM(quantity * 1) as total')
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $previousPrices = DB::table('product_prices')
            ->whereIn('product_id', $ids)
            ->where('type', PriceType::RETAIL)
            ->whereRaw('product_prices.id IN (
                SELECT MAX(pp2.id) FROM product_prices pp2
                WHERE pp2.product_id = product_prices.product_id
                  AND pp2.type = \'' . PriceType::RETAIL . '\'
                  AND pp2.id < (
                      SELECT MAX(pp3.id) FROM product_prices pp3
                      WHERE pp3.product_id = product_prices.product_id
                        AND pp3.type = \'' . PriceType::RETAIL . '\'
                  )
                GROUP BY pp2.product_id
            )')
            ->select('product_id', 'amount')
            ->get()
            ->keyBy('product_id');

        $result = [];
        foreach ($products as $item) {
            $imageData = $this->buildImageDataFromRow($item, '1');
            $imageNextData = !empty($item->photo2_file)
                ? $this->buildImageDataFromRow($item, '2')
                : $imageData;

            $reviewTotal = $reviewsCount->get($item->id)?->total ?? 0;
            $promo = $promotions->get($item->id);
            $qty = $quantities->get($item->id)?->total ?? 0;
            $prevPrice = $previousPrices->get($item->id)?->amount ?? 0;

            $result[] = [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'code' => $item->code,
                'price' => (float)($item->price ?? 0),
                'price_previous' => (float)$prevPrice,
                'quantity' => (float)$qty,
                'rating' => (float)$item->current_rating,
                'brand' => $item->brand_name,
                'priority' => (bool)$item->priority,
                'is_new' => $item->published_at && \Carbon\Carbon::parse($item->published_at)->gte(now()->subMonths(2)),
                'reduced' => (bool)$item->price_reduced,
                'only_on_order' => (bool)$item->only_on_order,
                'is_sale' => !(bool)$item->not_sale,
                'count_reviews' => $this->formatReviewCount($reviewTotal),
                'image' => $imageData,
                'image_next' => $imageNextData,
                'promotion' => $promo
                    ? ['has' => true, 'price' => (float)$promo->price, 'title' => $promo->title]
                    : ['has' => false, 'price' => 0.0, 'title' => ''],
            ];
        }

        return $result;
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

    public function getAttributesForCategory(int $categoryId): array
    {
        $cat = DB::table('categories')
            ->where('id', $categoryId)
            ->select(['_lft', '_rgt'])
            ->first();

        if (!$cat) return [];

        $productIds = $this->getProductIdsInCategory($categoryId);

        if (empty($productIds)) return [];

        $categoryIds = DB::table('categories')
            ->where('_lft', '<=', $cat->_lft)->where('_rgt', '>=', $cat->_rgt)
            ->orWhere(function ($q) use ($cat) {
                $q->where('_lft', '>=', $cat->_lft)->where('_rgt', '<=', $cat->_rgt);
            })
            ->pluck('id')->toArray();

        return $this->attributeQueryRepository->getAttributesByCategoryIds($categoryIds, $productIds);
    }

    public function getAggregates(int $categoryId): object
    {
        $productIds = $this->getProductIdsInCategory($categoryId);

        return $this->attributeQueryRepository->getAggregatesByProductIds($productIds);
    }

    public function getFilterAggregates(int $categoryId): object
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

        $productIds = $this->getProductIdsInCategory($categoryId);

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

    private function buildImageDataFromRow(\stdClass $row, string $suffix = '1'): array
    {
        $id = $row->{"photo{$suffix}_id"} ?? null;
        $file = $row->{"photo{$suffix}_file"} ?? '';
        $thumb = $row->{"photo{$suffix}_thumb"} ?? '';
        $alt = $row->{"photo{$suffix}_alt"} ?? '';
        $title = $row->{"photo{$suffix}_title"} ?? '';
        $description = $row->{"photo{$suffix}_description"} ?? '';

        $src = '/images/no-image.jpg';
        if (!empty($file) && $id) {
            $src = $this->photoService->getThumbUrl(
                photoId: (int)$id,
                modelType: self::PHOTO_MODEL_TYPE,
                imageableId: (int)$row->id,
                fileName: $file,
                thumb: 'catalog',
                isThumbEnabled: (bool)$thumb,
            );
        }

        return [
            'src' => $src,
            'alt' => $alt,
            'title' => $title,
            'description' => $description,
        ];
    }

    private function buildProductImage(\stdClass $item): string
    {
        if (empty($item->photo1_file)) return '/images/no-image.jpg';
        return $this->photoService->getThumbUrl(
            photoId: (int)$item->photo1_id,
            modelType: self::PHOTO_MODEL_TYPE,
            imageableId: (int)$item->id,
            fileName: $item->photo1_file,
            thumb: 'catalog',
            isThumbEnabled: (bool)$item->photo1_thumb,
        );
    }

    private function formatReviewCount(int $count): string
    {
        if ($count === 0) return '0 отзывов';

        $text = $count . ' отзыв';
        if ($count === 1 || ($count > 20 && $count % 10 === 1)) {
            return $text;
        }

        if (in_array($count, [2, 3, 4]) || (in_array($count % 10, [2, 3, 4]) && $count % 100 > 20)) {
            return $text . 'а';
        }

        return $text . 'ов';
    }
}
