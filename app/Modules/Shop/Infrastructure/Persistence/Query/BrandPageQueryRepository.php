<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomMainData;
use App\Modules\Shop\Application\Helpers\ImageInfoDataHelper;
use Illuminate\Support\Facades\DB;

class BrandPageQueryRepository
{
    private const string BRAND_MODEL_TYPE = 'catalog.brand';

    public function __construct(
        private readonly AttributeQueryRepository $attributeQueryRepository,
        private readonly ImageInfoDataHelper $imageInfoHelper,
    )
    {
    }

    public function getBrand(string $slug): ?CategoryRoomMainData
    {
        $row = DB::table('brands')
            ->leftJoin('photos', function ($join) {
                $join->on('brands.id', '=', 'photos.imageable_id')
                    ->where('photos.model_type', '=', self::BRAND_MODEL_TYPE)
                    ->where('photos.type', '=', 'image');
            })
            ->where('brands.slug', $slug)
            ->select(
                'brands.*',
                'photos.id as photo_id',
                'photos.file as photo_file',
                'photos.alt as photo_alt',
                'photos.title as photo_title',
                'photos.description as photo_description',
                'photos.format as photo_format',
                'photos.width as photo_width',
                'photos.height as photo_height',
                'photos.model_type as model_type',
            )
            ->first();
        if (!$row) return null;

        return new CategoryRoomMainData(
            id: $row->id,
            name: $row->name,
            slug: $row->slug,
            children: [],
            entity: 'brand',
            parent: null,
            totalProducts: 0,
            title: $row->name,
            description: $row->description ?? '',
            image: !empty($row->photo_id) ? $this->imageInfoHelper->build($row) : null,
        );
    }

    public function getProductIdsInBrand(int $brandId): array
    {
        return DB::table('products')
            ->where('products.brand_id', $brandId)
            ->where('products.published', true)
            ->where('products.not_sale', false)
            ->pluck('products.id')
            ->toArray();
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
}
