<?php

declare(strict_types=1);

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Catalog\Entity\Attribute;
use Illuminate\Support\Facades\DB;

class AttributeQueryRepository
{
    /**
     * Получить атрибуты для фильтрации по списку ID категорий и ID товаров.
     *
     * @param int[] $categoryIds ID категорий (обычно рутовые)
     * @param int[] $productIds  ID товаров, входящих в эти категории
     * @return array
     */
    public function getAttributesByCategoryIds(array $categoryIds, array $productIds): array
    {
        if (empty($categoryIds) || empty($productIds)) {
            return [];
        }

        // Получаем диапазоны _lft/_rgt для всех переданных категорий
        $categoryRanges = DB::table('categories')
            ->whereIn('id', $categoryIds)
            ->select(['_lft', '_rgt'])
            ->get();

        if ($categoryRanges->isEmpty()) {
            return [];
        }

        // Находим все ID категорий, входящих в эти диапазоны (наследники + они сами)
        $allCategoryIds = DB::table('categories')
            ->where(function ($q) use ($categoryRanges) {
                foreach ($categoryRanges as $range) {
                    $q->orWhere(function ($sq) use ($range) {
                        $sq->where('_lft', '>=', $range->_lft)
                           ->where('_rgt', '<=', $range->_rgt);
                    });
                }
            })
            ->pluck('id')
            ->toArray();

        if (empty($allCategoryIds)) {
            return [];
        }

        // Атрибуты, привязанные к этим категориям и к товарам
        $attrIdsFromCat = DB::table('attributes_categories')
            ->whereIn('category_id', $allCategoryIds)
            ->pluck('attribute_id')
            ->unique()
            ->toArray();

        $attrIdsFromProd = DB::table('attributes_products')
            ->whereIn('product_id', $productIds)
            ->pluck('attribute_id')
            ->unique()
            ->toArray();

        $attrIds = array_intersect($attrIdsFromCat, $attrIdsFromProd);
        if (empty($attrIds)) {
            return [];
        }

        $attributes = DB::table('attributes')
            ->whereIn('id', $attrIds)
            ->where('filter', true)
            ->orderBy('group_id')
            ->get();

        $result = [];
        foreach ($attributes as $attr) {
            $item = ['id' => $attr->id, 'name' => $attr->name, 'type' => $attr->type];

            if ($attr->type == 1) {
                $values = DB::table('attributes_products')
                    ->where('attribute_id', $attr->id)
                    ->whereIn('product_id', $productIds)
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
                    ->where('attribute_id', $attr->id)
                    ->whereIn('product_id', $productIds)
                    ->pluck('value');

                $variantIds = [];
                foreach ($values as $v) {
                    $d = json_decode($v);
                    if (is_array($d)) {
                        $variantIds = array_merge($variantIds, $d);
                    } else {
                        $variantIds[] = $d;
                    }
                }
                $variantIds = array_unique($variantIds);

                if (!empty($variantIds)) {
                    $variants = DB::table('attribute_variants')
                        ->whereIn('id', $variantIds)
                        ->select(['id', 'name'])
                        ->orderBy('name')
                        ->get();

                    $item['isVariant'] = true;
                    $item['variants'] = $variants->map(
                        fn($v) => ['id' => $v->id, 'name' => $v->name]
                    )->toArray();
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

    /**
     * Получить агрегированные данные (цены, бренды, теги) по списку товаров.
     *
     * @param int[] $productIds
     * @return object {min_price, max_price, brands[], tags[]}
     */
    public function getAggregatesByProductIds(array $productIds): object
    {
        if (empty($productIds)) {
            return (object)[
                'min_price' => 0,
                'max_price' => 0,
                'brands' => [],
                'tags' => [],
            ];
        }

        $priceData = DB::table('product_prices')
            ->whereIn('product_id', $productIds)
            ->where('type', PriceType::RETAIL)
            ->selectRaw('MIN(amount) as min_price, MAX(amount) as max_price')
            ->first();

        $brands = DB::table('products')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->whereIn('products.id', $productIds)
            ->where('products.published', true)
            ->select('brands.id', 'brands.name')
            ->distinct()
            ->orderBy('brands.name')
            ->get()
            ->toArray();

        $tags = DB::table('tags_products')
            ->join('tags', 'tags_products.tag_id', '=', 'tags.id')
            ->whereIn('tags_products.product_id', $productIds)
            ->select('tags.id', 'tags.name', 'tags.slug')
            ->distinct()
            ->orderBy('tags.name')
            ->get()
            ->toArray();

        return (object)[
            'min_price' => (float)($priceData->min_price ?? 0),
            'max_price' => (float)($priceData->max_price ?? 0),
            'brands' => $brands,
            'tags' => $tags,
        ];
    }

    /**
     * Получить полные данные для фильтров (агрегаты + атрибуты).
     *
     * @param int[] $categoryIds
     * @param int[] $productIds
     * @return object {min_price, max_price, brands[], tags[], attributes[]}
     */
    public function getFilterAggregatesByCategoryIdsAndProductIds(array $categoryIds, array $productIds): object
    {
        $aggregates = $this->getAggregatesByProductIds($productIds);
        $aggregates->attributes = $this->getAttributesByCategoryIds($categoryIds, $productIds);
        return $aggregates;
    }

    /**
     * Получить типы атрибутов.
     *
     * @param int[] $ids
     * @return array [id => type, ...]
     */
    public function getAttributeTypes(array $ids): array
    {
        if (empty($ids)) return [];
        return DB::table('attributes')
            ->whereIn('id', $ids)
            ->pluck('type', 'id')
            ->toArray();
    }

    /**
     * Применить фильтры к Eloquent-запросу товаров (Product).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     */
    public function applyFilters($query, array $filters): void
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

        $attrIds = [];
        foreach ($filters as $key => $value) {
            if (str_starts_with($key, 'a_')) {
                $attrIds[] = (int)substr($key, 2);
            }
        }
        $attrTypes = $this->getAttributeTypes($attrIds);

        foreach ($filters as $key => $value) {
            if (!str_starts_with($key, 'a_')) continue;
            $attrId = (int)substr($key, 2);
            $type = $attrTypes[$attrId] ?? null;
            if (!$type) continue;

            switch ($type) {
                case Attribute::TYPE_BOOL:
                    $query->whereHas('prod_attributes', fn($q) => $q->where('attribute_id', $attrId));
                    break;

                case Attribute::TYPE_INTEGER:
                case Attribute::TYPE_FLOAT:
                    $min = $value[0] ?? null;
                    $max = $value[1] ?? null;
                    if (!is_null($min) || !is_null($max)) {
                        $query->whereHas('prod_attributes', function ($q) use ($attrId, $min, $max) {
                            $q->where('attribute_id', $attrId);
                            if (!is_null($min)) {
                                $q->whereRaw('CAST(JSON_UNQUOTE(value) AS DECIMAL(10,2)) >= ?', [$min]);
                            }
                            if (!is_null($max)) {
                                $q->whereRaw('CAST(JSON_UNQUOTE(value) AS DECIMAL(10,2)) <= ?', [$max]);
                            }
                        });
                    }
                    break;

                case Attribute::TYPE_VARIANT:
                    $variantIds = (array) $value;
                    if (!empty($variantIds)) {
                        $query->where(function ($q) use ($attrId, $variantIds) {
                            // Товары без модификаций
                            $q->where(function ($sub) use ($attrId, $variantIds) {
                                $sub->doesntHave('modification')
                                    ->whereHas('prod_attributes', fn($attr) => $attr
                                        ->where('attribute_id', $attrId)
                                        ->whereIn('value', array_map(fn($v) => json_encode($v), $variantIds))
                                    );
                            });
                            // Товары с модификациями, у которых хотя бы одна вариация подходит
                            $q->orWhere(function ($sub) use ($attrId, $variantIds) {
                                $sub->whereHas('modification', function ($mod) use ($attrId, $variantIds) {
                                    $mod->whereHas('products', function ($prod) use ($attrId, $variantIds) {
                                        $prod->where('not_sale', false)
                                            ->whereHas('prod_attributes', fn($attr) => $attr
                                                ->where('attribute_id', $attrId)
                                                ->whereIn('value', array_map(fn($v) => json_encode($v), $variantIds))
                                            );
                                    });
                                });
                            });
                        });
                    }
                    break;
            }
        }
    }

    /**
     * Применить сортировку к Eloquent-запросу товаров (Product).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $order
     */
    public function applySorting($query, string $order): void
    {
        match ($order) {
            'price-down' => $query->reorder()
                ->orderByRaw('COALESCE((SELECT amount FROM product_prices WHERE product_id = products.id AND type = \'' . PriceType::RETAIL . '\' ORDER BY id DESC LIMIT 1), 0) DESC')
                ->orderBy('id'),
            'price-up' => $query->reorder()
                ->orderByRaw('COALESCE((SELECT amount FROM product_prices WHERE product_id = products.id AND type = \'' . PriceType::RETAIL . '\' ORDER BY id DESC LIMIT 1), 0) ASC')
                ->orderBy('id'),
            'name' => $query->reorder()->orderBy('name')->orderBy('id'),
            'rating' => $query->reorder()->orderBy('current_rating', 'desc')->orderBy('id'),
            default => $query->reorder()->orderBy('priority', 'desc')->orderBy('published_at', 'desc')->orderBy('id'),
        };
    }
}
