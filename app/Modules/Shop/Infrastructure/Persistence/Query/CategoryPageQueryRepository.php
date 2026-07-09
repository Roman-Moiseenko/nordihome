<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use Illuminate\Support\Facades\DB;

class CategoryPageQueryRepository
{
    private const string PHOTO_MODEL_TYPE = 'catalog.product';

    public function __construct(
        private readonly PhotoService $photoService,
    )
    {
    }

    public function getCategory(string $slug): ?Category
    {
        return Category::where('slug', $slug)
            ->select(['id', 'name', 'slug', 'svg', 'meta', 'parent_id', '_lft', '_rgt'])
            ->first();
    }

    public function getProductIds(array $filters, int $categoryId, int $page, int $perPage): array
    {
        $cat = DB::table('categories')
            ->where('id', $categoryId)
            ->select(['_lft', '_rgt'])
            ->first();

        if (!$cat) {
            return ['ids' => [], 'total' => 0];
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
            return ['ids' => [], 'total' => 0];
        }

        $allIds = $productIdsInCategory->toArray();

        $order = $filters['order'] ?? 'name';

        $query = Product::whereIn('id', $allIds)
            ->where('published', true)
            ->where('not_sale', false);

        $this->applyFilters($query, $filters);

        $filteredIds = $query->pluck('id')->toArray();
        $total = count($filteredIds);

        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $pageIds = array_slice($filteredIds, $offset, $perPage);

        return ['ids' => $pageIds, 'total' => $total];
    }

    public function loadProductCards(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $products = DB::table('products')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('product_prices', function ($join) {
                $join->on('products.id', '=', 'product_prices.product_id')
                    ->where('product_prices.type', '=', 'retail')
                    ->whereRaw('product_prices.id = (
                        SELECT MAX(pp2.id) FROM product_prices pp2
                        WHERE pp2.product_id = products.id AND pp2.type = \'retail\'
                    )');
            })
            ->leftJoin('photos', function ($join) {
                $join->on('products.id', '=', 'photos.imageable_id')
                    ->where('photos.model_type', '=', self::PHOTO_MODEL_TYPE)
                    ->where('photos.type', '=', 'image');
            })
            ->whereIn('products.id', $ids)
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
                'brands.name as brand_name',
                'brands.slug as brand_slug',
                'product_prices.amount as price',
                'photos.id as photo_id',
                'photos.file as photo_file',
                'photos.thumb as photo_thumb',
            )
            ->get();

        $result = [];
        foreach ($products as $item) {
            $result[] = [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'code' => $item->code,
                'price' => (float)($item->price ?? 0),
                'rating' => (float)$item->current_rating,
                'brand' => [
                    'name' => $item->brand_name,
                    'slug' => $item->brand_slug,
                ],
                'image' => $this->buildProductImage($item),
                'priority' => (bool)$item->priority,
                'is_new' => $item->published_at && \Carbon\Carbon::parse($item->published_at)->gte(now()->subMonths(2)),
                'reduced' => (bool)$item->price_reduced,
                'only_on_order' => (bool)$item->only_on_order,
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

        $attrIdsFromCat = DB::table('attribute_category')
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
                foreach ($values as $v) { $decoded[] = (int)json_decode($v); }
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
            ->whereIn('product_id', $productIds)->where('type', 'retail')
            ->selectRaw('MIN(amount) as min_price, MAX(amount) as max_price')->first();

        $brands = DB::table('products')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->whereIn('products.id', $productIds)->where('products.published', true)
            ->select('brands.id', 'brands.name', 'brands.slug')->distinct()->orderBy('brands.name')
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

    private function buildProductImage(\stdClass $item): string
    {
        if (empty($item->photo_file)) return '';
        return $this->photoService->getThumbUrl(
            photoId: (int)$item->photo_id,
            modelType: self::PHOTO_MODEL_TYPE,
            imageableId: (int)$item->id,
            fileName: $item->photo_file,
            thumb: 'catalog',
            isThumbEnabled: (bool)$item->photo_thumb,
        );
    }
}
