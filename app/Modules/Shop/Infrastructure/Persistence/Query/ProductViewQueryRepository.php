<?php

declare(strict_types=1);

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Base\Entity\Dimensions;
use App\Modules\Catalog\Infrastructure\Models\Attribute;
use App\Modules\Discount\Domain\ValueObjects\PromotionStatus;
use App\Modules\Shared\Application\Actions\GetImageThumbByRowUseCase;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\DTOs\Elements\DimensionsData;
use App\Modules\Shop\Application\DTOs\Elements\ImageInfoData;
use App\Modules\Shop\Application\DTOs\Elements\PromotionProductData;
use App\Modules\Shop\Application\DTOs\Entities\ProductData;
use Illuminate\Support\Facades\DB;

class ProductViewQueryRepository
{
    private const string PHOTO_MODEL_TYPE = 'catalog.product';

    public function __construct(
        private readonly GetImageThumbByRowUseCase $imageThumbUseCase,

    )
    {
    }

    public function getProductBySlug(string $slug, ClientContext $client): ProductData
    {
        $now = now();
        // Single comprehensive query with subqueries for:
        //   - current retail price
        //   - previous retail price
        //   - active promotion
        //   - published reviews count
        //   - brand logo (image)
        //   - wish and cart flags for the client
        $query = DB::table('products')
            ->where('products.slug', $slug)
            ->join('categories', 'products.main_category_id', '=', 'categories.id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('promotions_products', function ($join) use ($now) {
                $join->on('products.id', '=', 'promotions_products.product_id')
                    ->join('promotions', function ($join) use ($now) {
                        $join->on('promotions_products.promotion_id', '=', 'promotions.id')
                            ->where('promotions.status', PromotionStatus::STARTED);
                            //->where('promotions.start_at', '<=', $now)
                            //->where('promotions.finish_at', '>=', $now);
                    });
            });

        $isWishSelect = '0 as is_wish';
        $inCartSelect = '0 as in_cart';
        if ($client->id !== null) {
            $query->leftJoin('wishes', function ($join) use ($client) {
                $join->on('products.id', '=', 'wishes.product_id')
                    ->where('wishes.client_id', '=', $client->id);
            });
            $isWishSelect = 'CASE WHEN wishes.id IS NOT NULL THEN 1 ELSE 0 END as is_wish';

            $query->leftJoin('cart_storage', function ($join) use ($client) {
                $join->on('products.id', '=', 'cart_storage.product_id')
                    ->where('cart_storage.client_id', '=', $client->id);
            });
            $inCartSelect = 'CASE WHEN cart_storage.id IS NOT NULL THEN 1 ELSE 0 END as in_cart';
        } elseif ($client->uuid !== null) {
            $query->leftJoin('cart_cookie', function ($join) use ($client) {
                $join->on('products.id', '=', 'cart_cookie.product_id')
                    ->where('cart_cookie.user_ui', '=', $client->uuid);
            });
            $inCartSelect = 'CASE WHEN cart_cookie.id IS NOT NULL THEN 1 ELSE 0 END as in_cart';
        }

        $row = $query
            ->select(
                'products.id',
                'products.name',
                'products.slug',
                'products.code',
                'products.description',
                'products.short',
                'products.care',
                'products.current_rating',
                'products.not_sale',
                'products.price_reduced',
                'products.published_at',
                'products.local',
                'products.delivery',
                'products.dimensions',
                'products.packages',
                'products.brand_id',
                'products.only_on_order',
                // Category
                'categories.name as category_name',
                // Brand
                'brands.name as brand_name',
                // Current retail price (latest)
                DB::raw("(
                    SELECT pp.amount FROM product_prices pp
                    WHERE pp.product_id = products.id
                      AND pp.type = '" . $client->priceType . "'
                    ORDER BY pp.set_at DESC, pp.id DESC
                    LIMIT 1
                ) as price"),
                // Previous retail price
                DB::raw("(
                    SELECT pp.amount FROM product_prices pp
                    WHERE pp.product_id = products.id
                      AND pp.type = '" . $client->priceType . "'
                      AND pp.id < (
                          SELECT MAX(pp2.id) FROM product_prices pp2
                          WHERE pp2.product_id = products.id
                            AND pp2.type = '" . $client->priceType . "'
                      )
                    ORDER BY pp.set_at DESC, pp.id DESC
                    LIMIT 1
                ) as price_previous"),
                // Published reviews count
                DB::raw("(
                    SELECT COUNT(*) FROM product_reviews
                    WHERE product_reviews.product_id = products.id
                      AND product_reviews.status = '". \App\Modules\Catalog\Entity\Review::STATUS_PUBLISHED . "'
                ) as count_reviews"),

                // Promotion data
                'promotions_products.price as promotion_price',
                'promotions.name as promotion_name',

                'promotions.color_class as color_class',
                'promotions.position_class as position_class',
                'promotions.text_tag as text_tag',
                'promotions.show_tag as show_tag',
                'promotions.show_discount as show_discount',

                // Brand logo
                DB::raw("(
                    SELECT file FROM photos
                    WHERE imageable_id = brands.id
                      AND model_type = 'catalog.brand'
                      AND type = 'image'
                    LIMIT 1
                ) as brand_logo_file"),
                DB::raw($isWishSelect),
                DB::raw($inCartSelect),
            )
            ->first();

        if (!$row) {
            throw new \DomainException("Product not found by slug: {$slug}");
        }

        // Get all photos sorted by sort (separate query for gallery)
        $photoRows = DB::table('photos')
            ->where('imageable_id', $row->id)
            ->where('model_type', self::PHOTO_MODEL_TYPE)
            ->where('type', 'gallery')
            ->orderBy('sort')
            ->get(['id', 'file', 'alt', 'title', 'description', 'model_type']);

        // Build ImageInfoData array
        $images = [];
        foreach ($photoRows as $photo) {

            // Подготавливаем stdClass для UseCase
            $photoRow = new \StdClass();
            $photoRow->id = (int)$row->id;        // ID товара
            $photoRow->photo_id = (int)$photo->id;
            $photoRow->photo_file = $photo->file;
            $photoRow->model_type = self::PHOTO_MODEL_TYPE;

            // Используем UseCase для получения трёх вариантов URL
            $src = $this->imageThumbUseCase->execute($photoRow, 'card');
            $mini = $this->imageThumbUseCase->execute($photoRow, 'mini');
            $full = $this->imageThumbUseCase->execute($photoRow, 'original');


            $images[] = ImageInfoData::fromArray([
                'full' => $full,
                'src' => $src,
                'alt' => $photo->alt ?? '',
                'title' => $photo->title ?? '',
                'description' => $photo->description ?? '',
                'mini' => $mini,
            ]);
        }

        // Price
        $price = (float)($row->price ?? 0);

        // Promotion data
        $promotion = new PromotionProductData(
            has: $row->promotion_price !== null,
            name: $row->promotion_name ?? '',
            price: (float)($row->promotion_price ?? 0),
            color: $row->color_class ?? '',
            position: $row->position_class ?? '',
            text: $row->text_tag ?? '',
            showTag: (bool)$row->show_tag ?? false,
            showDiscount: (bool)$row->show_discount ?? false,
        );

        // Dimensions
        $dimensions = $this->parseDimensions($row->dimensions, $row->packages);

        // Brand logo URL
        $brandLogo = '';
        if (!empty($row->brand_logo_file)) {
            $brandLogo = '/uploads/brand/' . $row->brand_id . '/' . $row->brand_logo_file;
        }

        return new ProductData(
            id: $row->id,
            name: $row->name,
            slug: $row->slug,
            code: $row->code,
            categoryName: $row->category_name,
            images: $images,
            is_wish: (bool)$row->is_wish,
            in_cart: (bool)$row->in_cart,
            count_reviews: (int)$row->count_reviews,
            rating: (float)($row->current_rating ?? 0),
            is_sale: !(bool)$row->not_sale,
            promotion: $promotion,
            price: $price,
            price_previous: (float)($row->price_previous ?? 0),
            public: 0,
            is_new: $row->published_at && \Carbon\Carbon::parse($row->published_at)->gte(now()->subMonths(2)),
            brandLogo: $brandLogo,
            brandName: $row->brand_name ?? '',
            description: $row->description ?? '',
            dimensions: $dimensions,
            isRegion: (bool)$row->local,
            isDelivery: (bool)$row->delivery, //TODO Кол-во товаров на складах
            short: $row->short,
            care: $row->care,
            only_on_order: (bool)$row->only_on_order,
        );
    }

    private function parseDimensions(?string $dimensionsJson, ?string $packagesJson): DimensionsData
    {
        $dims = json_decode($dimensionsJson ?? '{}', true);
        $packs = json_decode($packagesJson ?? '[]', true);

        $width = (float)($dims['width'] ?? 0);
        $height = (float)($dims['height'] ?? 0);
        $depth = (float)($dims['depth'] ?? 0);
        $weight = (float)($dims['weight'] ?? 0);
        $measure = $dims['measure'] ?? 'г';
        $type = (int)($dims['type'] ?? Dimensions::TYPE_DEPTH);

        // Use package weight if available (matching Product model's weight() logic)
        if (!empty($packs) && isset($packs[0]['weight'])) {
            // packages.weight is in kg already
            $weight = (float)$packs[0]['weight'];
        } elseif ($measure === 'г' && $weight > 0) {
            $weight = $weight / 1000;
        }

        // Volume in m³
        $volume = ($height * $width * $depth) / 1_000_000;
        $volume = max(0, ceil($volume * 10000) / 10000);

        $captions = Dimensions::CAPTION_TYPES[$type] ?? ['Высота', 'Ширина', 'Глубина'];

        return new DimensionsData(
            height: $height,
            width: $width,
            depth: $depth,
            weight: ceil($weight * 1000) / 1000,
            volume: $volume,
            captions: $captions,
        );
    }

    public function getAttributes(ProductData $product): array
    {
        $productId = $product->id;

        // Получаем все атрибуты товара с типом, группой и значением
        // и все варианты для variant-атрибутов
        $rows = DB::table('attributes')
            ->join('attributes_products', 'attributes.id', '=', 'attributes_products.attribute_id')
            ->leftJoin('attribute_groups', 'attributes.group_id', '=', 'attribute_groups.id')
            ->leftJoin('attribute_variants', function ($join) {
                $join->on('attributes.id', '=', 'attribute_variants.attribute_id')
                    ->where('attributes.type', '=', Attribute::TYPE_VARIANT);
            })
            ->where('attributes_products.product_id', $productId)
            ->select(
                'attributes.id',
                'attributes.name',
                'attributes.type',
                'attribute_groups.name as group_name',
                'attributes_products.value',
                'attribute_variants.id as variant_id',
                'attribute_variants.name as variant_name',
            )
            ->orderBy('attribute_groups.id')
            ->orderBy('attributes.id')
            ->get();

        // Собираем в структуру [attribute_id => ['id', 'name', 'type', 'group', 'pivot_value', 'variants' => [id => name]]]
        $attrs = [];
        foreach ($rows as $row) {
            if (!isset($attrs[$row->id])) {
                $attrs[$row->id] = [
                    'id' => $row->id,
                    'name' => $row->name,
                    'type' => $row->type,
                    'group_name' => $row->group_name ?? 'Прочее',
                    'pivot_value' => $row->value,
                    'variants' => [],
                ];
            }
            if ($row->variant_id !== null) {
                $attrs[$row->id]['variants'][$row->variant_id] = $row->variant_name;
            }
        }

        $result = [];
        foreach ($attrs as $attr) {
            $value = json_decode($attr['pivot_value'], true);

            // Для variant-атрибутов преобразуем id в имена
            if ($attr['type'] == Attribute::TYPE_VARIANT && !empty($attr['variants'])) {
                if (is_array($value)) {
                    $names = [];
                    foreach ($value as $vid) {
                        $names[] = $attr['variants'][(int)$vid] ?? (string)$vid;
                    }
                    $value = implode(', ', $names);
                } else {
                    $value = $attr['variants'][(int)$value] ?? (string)$value;
                }
            }

            // Для bool
            if ($attr['type'] == Attribute::TYPE_BOOL) {
                $value = true;
            }

            $groupName = $attr['group_name'];
            if (!isset($result[$groupName])) {
                $result[$groupName] = [];
            }

            $result[$groupName][] = [
                'name' => $attr['name'],
                'value' => $value,
            ];
        }

        return $result;
    }
}
