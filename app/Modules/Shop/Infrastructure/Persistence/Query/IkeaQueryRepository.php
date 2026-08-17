<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use App\Modules\Shared\Application\Actions\GetImageThumbByRowUseCase;
use App\Modules\Shop\Application\DTOs\Elements\IkeaVariantData;
use App\Modules\Shop\Application\DTOs\Entities\IkeaCategoryMainData;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class IkeaQueryRepository
{
    private const string PHOTO_MODEL_TYPE = 'parser.product';

    public function __construct(
        private readonly GetImageThumbByRowUseCase $imageThumbUseCase,
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
                'parser_products.*',
                DB::raw("(SELECT id FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_id"),
                DB::raw("(SELECT file FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_file"),
                DB::raw("(SELECT model_type FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' LIMIT 1) as model_type"),
                DB::raw("(SELECT alt FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_alt"),
                DB::raw("(SELECT title FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_title"),
                DB::raw("(SELECT description FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 0 LIMIT 1) as photo1_description"),
                DB::raw("(SELECT id FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 1 LIMIT 1) as photo2_id"),
                DB::raw("(SELECT file FROM photos WHERE imageable_id = parser_products.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'gallery' AND sort = 1 LIMIT 1) as photo2_file"),
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

        $alt = $row->{"photo{$suffix}_alt"} ?? '';
        $title = $row->{"photo{$suffix}_title"} ?? '';
        $description = $row->{"photo{$suffix}_description"} ?? '';

        $photoRow = new \stdClass();
        $photoRow->photo_id      = $id;
        $photoRow->photo_file    = $file;
        $photoRow->model_type    = $row->model_type;
        $photoRow->id  = (int) $row->id;

        $src = $this->imageThumbUseCase->execute($photoRow, 'catalog');

        return [
            'src' => $src,
            'alt' => $alt,
            'title' => $title,
            'description' => $description,
        ];
    }

    public function getProductBySlug(string $slug): array
    {
        $row = DB::table('parser_products')
            ->where('slug', $slug)
            ->first();

        if (!$row) return [];
        return $this->hydrate($row);
    }

    public function getProductByCode(string $code): array
    {
        $row = DB::table('parser_products')
            ->where('code', $code)
            ->first();

        if (!$row) return [];
        return $this->hydrate($row);
    }

    private function hydrate(\StdClass $row)
    {
        $photos = DB::table('photos')
            ->where('imageable_id', $row->id)
            ->where('model_type', self::PHOTO_MODEL_TYPE)
            ->where('type', 'gallery')
            ->orderBy('sort')
            ->get(['id', 'file', 'model_type', 'alt', 'title', 'description']);

        $images = [];
        foreach ($photos as $photo) {
            // Подготавливаем stdClass для UseCase
            $photoRow = new \StdClass();
            $photoRow->id          = (int) $row->id;        // ID товара
            $photoRow->photo_id    = (int) $photo->id;
            $photoRow->photo_file  = $photo->file;
            $photoRow->model_type  = self::PHOTO_MODEL_TYPE;

            // Используем UseCase для получения трёх вариантов URL
            $src  = $this->imageThumbUseCase->execute($photoRow, 'card');
            $mini = $this->imageThumbUseCase->execute($photoRow, 'mini');
            $full = $this->imageThumbUseCase->execute($photoRow, 'original');

            $images[] = [
                'src' => $src,
                'alt' => $photo->alt ?? '',
                'title' => $photo->title ?? '',
                'description' => $photo->description ?? '',
                'mini' => $mini,
                'full' => $full,
            ];
        }
        $composite = array_filter(array_map(function ($item) {
            return $this->getShortProduct($item['code']);
        }, isset($row->composite) ? json_decode($row->composite, true) : []));

        $variants = array_filter(array_map(function ($item) {
            return $this->getShortProduct($item);
        }, isset($row->variants) ? json_decode($row->variants, true) : []));

        return [
            'id' => $row->id,
            'name' => $row->name,
            'code' => $row->code,
            'slug' => $row->slug,
            'model' => $row->model ?? '',
            'price' => (float)($row->price_sell ?? 0),
            'short' => $row->short ?? '',
            'description' => $row->description ?? '',
            'fragile' => (bool)($row->fragile ?? false),
            'sanctioned' => (bool)($row->sanctioned ?? false),
            'availability' => (bool)($row->availability ?? false),
            'packs' => (int)($row->packs ?? 1),
            'composite' => $composite,
            'quantity' => isset($row->quantity) ? json_decode($row->quantity, true) : [],
            'colors' => isset($row->colors) ? json_decode($row->colors, true) : [],
            'packages' => isset($row->packages) ? json_decode($row->packages, true) : [],
            'images' => $images,
            'materials' => isset($row->materials) ? json_decode($row->materials, true) : [],
            'care' => $row->care,
            'dimensions' => isset($row->dimensions) ? json_decode($row->dimensions, true) : [],
            'variants' => $variants,
        ];
    }


    private function getShortProduct(string $code):? IkeaVariantData
    {
        $row = DB::table('parser_products')
            ->where('code', $code)
            ->first();

        if (is_null($row)) return null;

        $photo = DB::table('photos')
            ->where('imageable_id', $row->id)
            ->where('model_type', self::PHOTO_MODEL_TYPE)
            ->where('type', 'gallery')
            ->orderBy('sort')
            ->first(['id', 'file', 'model_type']);

        $photoRow = new \StdClass();
        $photoRow->id          = (int) $row->id;        // ID товара
        $photoRow->photo_id    = (int) $photo->id;
        $photoRow->photo_file  = $photo->file;
        $photoRow->model_type  = self::PHOTO_MODEL_TYPE;

        $image_mini = $this->imageThumbUseCase->execute($photoRow, 'mini');

        return new IkeaVariantData(
            id: $row->id,
            name: $row->name,
            code: $code,
            image: $image_mini,
        );
    }
}
