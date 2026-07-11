<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductIndexQueryRepository
{
    private const string PHOTO_MODEL_TYPE = 'catalog.product';
    public function __construct(
        private AttributeQueryRepository $attributeQueryRepository,
        private readonly PhotoService $photoService,
    )
    {
    }

    public function getFilterSortPaginationProducts(array $filters, $allIds, int $page, int $perPage): LengthAwarePaginator
    {

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
