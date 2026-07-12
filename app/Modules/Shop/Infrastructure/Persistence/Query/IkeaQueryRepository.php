<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use App\Modules\Shop\Application\DTOs\Entities\IkeaCategoryMainData;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class IkeaQueryRepository
{
    private const string PHOTO_MODEL_TYPE = 'parser.product';

    public function __construct(
        private readonly PhotoService $photoService,
    )
    {
    }

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
     * @return
     */
    public function getProductIdsInCategory(int $id): ?array
    {
        $cat = DB::table('parser_categories')
            ->where('id', $id)
            ->select(['_lft', '_rgt'])
            ->first();

        if (!$cat) return null;

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

    public function loadProductCards(mixed $allProductIds, int $page, int $perPage): array
    {
        $orderedIds = implode(',', array_map('intval', $allProductIds));

        $rows = DB::table('parser_products')
            ->whereIn('parser_products.id', $allProductIds)
            ->orderByRaw("FIELD(parser_products.id, $orderedIds)")
            ->select(
                'parser_products.id',
                'parser_products.name',
                'parser_products.slug',
                'parser_products.code',
                'parser_products.price_sell',
                'parser_products.short',
                DB::raw("(SELECT id FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_id"),
                DB::raw("(SELECT file FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_file"),
                DB::raw("(SELECT thumb FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_thumb"),
                DB::raw("(SELECT alt FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_alt"),
                DB::raw("(SELECT title FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_title"),
                DB::raw("(SELECT description FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_description"),
                DB::raw("(SELECT id FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 1 LIMIT 1) as photo2_id"),
                DB::raw("(SELECT file FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 1 LIMIT 1) as photo2_file"),
                DB::raw("(SELECT thumb FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 1 LIMIT 1) as photo2_thumb"),
                DB::raw("(SELECT alt FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 1 LIMIT 1) as photo2_alt"),
                DB::raw("(SELECT title FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 1 LIMIT 1) as photo2_title"),
                DB::raw("(SELECT description FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 1 LIMIT 1) as photo2_description"),
            )
            ->forPage($page, $perPage)
            ->get();

        $result = [];
        foreach ($rows as $item) {
            $imageData = $this->buildImageDataFromRow($item, '1');
            $imageNextData = !empty($item->photo2_file)
                ? $this->buildImageDataFromRow($item, '2')
                : $imageData;

            $result[] = [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'code' => $item->code,
                'price_sell' => (float)($item->price_sell ?? 0),
                'short' => $item->short ?? '',
                'image' => $imageData,
                'image_next' => $imageNextData,
            ];
        }

        return $result;
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

        $products = $this->loadProductCards($allProductIds, $page, $perPage);

        return new LengthAwarePaginator(
            items: collect($products),
            total: count($allProductIds),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => request()->url(),
                'query' => request()->query(),
            ],
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
}
