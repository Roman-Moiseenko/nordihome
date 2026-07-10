<?php

declare(strict_types=1);

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CategoryPageQueryRepository
{
    private const string PHOTO_MODEL_TYPE = 'catalog.product';

    public function __construct(
        private readonly PhotoService $photoService,
    )
    {
    }

    public function getCategory(string $slug): ?object
    {
        return Category::with(['parent:id,name,slug', 'children:id,name,slug,parent_id'])
            ->where('slug', $slug)
            ->select(['id', 'name', 'slug', 'svg', 'meta', 'parent_id', '_lft', '_rgt'])
            ->first();
    }

    public function getProductIds(array $filters, int $categoryId, int $page, int $perPage): LengthAwarePaginator
    {
        $cat = DB::table('categories')
            ->where('id', $categoryId)
            ->select(['_lft', '_rgt'])
            ->first();

        if (!$cat) {
            return new LengthAwarePaginator(
                items: collect([]),
                total: 0,
                perPage: $perPage,
                currentPage: $page,
                options: ['path' => request()->url(), 'query' => request()->query()],
            );
        }

        $productIdsInCategory = DB::table('products')
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
            ->pluck('products.id');

        if ($productIdsInCategory->isEmpty()) {
            return new LengthAwarePaginator(
                items: collect([]),
                total: 0,
                perPage: $perPage,
                currentPage: $page,
                options: ['path' => request()->url(), 'query' => request()->query()],
            );
        }

        $allIds = $productIdsInCategory->toArray();

        $query = Product::whereIn('id', $allIds);

        $this->applySorting($query, $filters['order'] ?? '');
        $this->applyFilters($query, $filters);

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

    public function loadProductCards(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $products = DB::table('products')
            ->whereIn('products.id', $ids)
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
                'image' => $this->buildProductImage($item),
                'priority' => (bool)$item->priority,
                'is_new' => $item->published_at && \Carbon\Carbon::parse($item->published_at)->gte(now()->subMonths(2)),
                'reduced' => (bool)$item->price_reduced,
                'only_on_order' => (bool)$item->only_on_order,
                'is_sale' => !(bool)$item->not_sale,
                'count_reviews' => $this->formatReviewCount($reviewTotal),
                'images' => $imageData,
                'images_next' => $imageNextData,
                'promotion' => $promo
                    ? ['has' => true, 'price' => (float)$promo->price, 'title' => $promo->title]
                    : ['has' => false, 'price' => 0.0, 'title' => ''],
            ];
        }

        return $result;
    }

    public function getAttributesForCategory(int $categoryId): array
    {
        $cat = DB::table('categories')
            ->where('id', $categoryId)
            ->select(['_lft', '_rgt'])
            ->first();

        if (!$cat) return [];

        $productIds = DB::table('products')
            ->where('published', true)->where('not_sale', false)
            ->where(function ($q) use ($cat) {
                $q->whereExists(function ($sq) use ($cat) {
                    $sq->select(DB::raw(1))->from('categories')
                        ->whereColumn('categories.id', 'products.main_category_id')
                        ->where('categories._lft', '>=', $cat->_lft)
                        ->where('categories._rgt', '<=', $cat->_rgt);
                })
                    ->orWhereExists(function ($sq) use ($cat) {
                        $sq->select(DB::raw(1))->from('categories_products')
                            ->whereColumn('categories_products.product_id', 'products.id')
                            ->join('categories', 'categories.id', '=', 'categories_products.category_id')
                            ->where('categories._lft', '>=', $cat->_lft)
                            ->where('categories._rgt', '<=', $cat->_rgt);
                    });
            })
            ->pluck('id')->toArray();

        if (empty($productIds)) return [];

        $categoryIds = DB::table('categories')
            ->where('_lft', '<=', $cat->_lft)->where('_rgt', '>=', $cat->_rgt)
            ->orWhere(function ($q) use ($cat) {
                $q->where('_lft', '>=', $cat->_lft)->where('_rgt', '<=', $cat->_rgt);
            })
            ->pluck('id')->toArray();

        $attrIdsFromCat = DB::table('attributes_categories')
            ->whereIn('category_id', $categoryIds)
            ->pluck('attribute_id')->unique()->toArray();

        $attrIdsFromProd = DB::table('attributes_products')
            ->whereIn('product_id', $productIds)
            ->pluck('attribute_id')->unique()->toArray();

        $attrIds = array_intersect($attrIdsFromCat, $attrIdsFromProd);
        if (empty($attrIds)) return [];

        $attributes = DB::table('attributes')
            ->whereIn('id', $attrIds)->where('filter', true)
            ->orderBy('group_id')->get();

        $result = [];
        foreach ($attributes as $attr) {
            $item = ['id' => $attr->id, 'name' => $attr->name, 'type' => $attr->type];

            if ($attr->type == 1) {
                $values = DB::table('attributes_products')
                    ->where('attribute_id', $attr->id)->whereIn('product_id', $productIds)
                    ->pluck('value');
                $decoded = [];
                foreach ($values as $v) {
                    $decoded[] = (int)json_decode($v);
                }
                if (!empty($decoded)) {
                    $item['isNumeric'] = true;
                    $item['min'] = min($decoded);
                    $item['max'] = max($decoded);
                }
            } elseif ($attr->type == 3) {
                $values = DB::table('attributes_products')
                    ->where('attribute_id', $attr->id)->whereIn('product_id', $productIds)
                    ->pluck('value');
                $variantIds = [];
                foreach ($values as $v) {
                    $d = json_decode($v);
                    if (is_array($d)) $variantIds = array_merge($variantIds, $d);
                    else $variantIds[] = $d;
                }
                $variantIds = array_unique($variantIds);
                if (!empty($variantIds)) {
                    $variants = DB::table('attribute_variants')
                        ->whereIn('id', $variantIds)->select(['id', 'name'])->orderBy('name')->get();
                    $item['isVariant'] = true;
                    $item['variants'] = $variants->map(fn($v) => ['id' => $v->id, 'name' => $v->name])->toArray();
                }
            } elseif ($attr->type == 2) {
                $item['isBool'] = true;
            }

            if (isset($item['isNumeric']) || isset($item['isVariant']) || isset($item['isBool'])) {
                $result[] = $item;
            }
        }

        return $result;
    }

    public function getAggregates(int $categoryId): object
    {
        $cat = DB::table('categories')
            ->where('id', $categoryId)->select(['_lft', '_rgt'])->first();

        if (!$cat) {
            return (object)['min_price' => 0, 'max_price' => 0, 'brands' => [], 'tags' => []];
        }

        $productIds = DB::table('products')
            ->where('published', true)->where('not_sale', false)
            ->where(function ($q) use ($cat) {
                $q->whereExists(function ($sq) use ($cat) {
                    $sq->select(DB::raw(1))->from('categories')
                        ->whereColumn('categories.id', 'products.main_category_id')
                        ->where('categories._lft', '>=', $cat->_lft)->where('categories._rgt', '<=', $cat->_rgt);
                })->orWhereExists(function ($sq) use ($cat) {
                    $sq->select(DB::raw(1))->from('categories_products')
                        ->whereColumn('categories_products.product_id', 'products.id')
                        ->join('categories', 'categories.id', '=', 'categories_products.category_id')
                        ->where('categories._lft', '>=', $cat->_lft)->where('categories._rgt', '<=', $cat->_rgt);
                });
            })
            ->pluck('id')->toArray();

        if (empty($productIds)) {
            return (object)['min_price' => 0, 'max_price' => 0, 'brands' => [], 'tags' => []];
        }

        $priceData = DB::table('product_prices')
            ->whereIn('product_id', $productIds)->where('type', PriceType::RETAIL)
            ->selectRaw('MIN(amount) as min_price, MAX(amount) as max_price')->first();

        $brands = DB::table('products')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->whereIn('products.id', $productIds)->where('products.published', true)
            ->select('brands.id', 'brands.name')->distinct()->orderBy('brands.name')
            ->get()->toArray();

        $tags = DB::table('tags_products')
            ->join('tags', 'tags_products.tag_id', '=', 'tags.id')
            ->whereIn('tags_products.product_id', $productIds)
            ->select('tags.id', 'tags.name', 'tags.slug')->distinct()->orderBy('tags.name')
            ->get()->toArray();

        return (object)[
            'min_price' => (float)($priceData->min_price ?? 0),
            'max_price' => (float)($priceData->max_price ?? 0),
            'brands' => $brands,
            'tags' => $tags,
        ];
    }

    public function getFilterAggregates(int $categoryId): object
    {
        $aggregates = $this->getAggregates($categoryId);
        $aggregates->attributes = $this->getAttributesForCategory($categoryId);
        return $aggregates;
    }

    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['price'])) {
            $min = (float)($filters['price'][0] ?? 0);
            $max = (float)($filters['price'][1] ?? 0);
            if ($min > 0 || $max > 0) {
                $query->whereHas('prices', function ($q) use ($min, $max) {
                    $q->where('type', PriceType::RETAIL);
                    if ($min > 0) $q->where('amount', '>=', $min);
                    if ($max > 0) $q->where('amount', '<=', $max);
                });
            }
        }

        if (!empty($filters['brands'])) {
            $query->whereIn('brand_id', $filters['brands']);
        }
        if (!empty($filters['tag_id'])) {
            $query->whereHas('tags', fn($q) => $q->where('id', $filters['tag_id']));
        }
        foreach ($filters as $key => $value) {
            if (str_starts_with($key, 'a_')) {
                $attrId = (int)substr($key, 2);
                $attr = \App\Modules\Catalog\Entity\Attribute::find($attrId);
                if (!$attr) continue;
                if ($attr->isBool()) {
                    $query->whereHas('prod_attributes', fn($q) => $q->where('attribute_id', $attrId));
                }
            }
        }
    }

    private function applySorting($query, string $order): void
    {
        match ($order) {
            'price-down' => $query
                ->orderByRaw('COALESCE((SELECT amount FROM product_prices WHERE product_id = products.id AND type = \'' . PriceType::RETAIL . '\' ORDER BY id DESC LIMIT 1), 0) DESC')
                ->orderBy('id'),
            'price-up' => $query
                ->orderByRaw('COALESCE((SELECT amount FROM product_prices WHERE product_id = products.id AND type = \'' . PriceType::RETAIL . '\' ORDER BY id DESC LIMIT 1), 0) ASC')
                ->orderBy('id'),
            'name' => $query->orderBy('name')->orderBy('id'),
            'rating' => $query->orderBy('current_rating', 'desc')->orderBy('id'),
            default => $query->orderBy('priority', 'desc')->orderBy('published_at', 'desc')->orderBy('id'),
        };
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
