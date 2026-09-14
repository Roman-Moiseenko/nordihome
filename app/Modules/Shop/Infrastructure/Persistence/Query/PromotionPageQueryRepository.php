<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shared\Infrastructure\Services\PhotoService;
use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomMainData;
use App\Modules\Shop\Application\Helpers\ImageInfoDataHelper;
use Illuminate\Support\Facades\DB;

class PromotionPageQueryRepository
{
    private const string PROMOTION_MODEL_TYPE = 'discount.promotion';

    public function __construct(
        private readonly ImageInfoDataHelper $imageInfoHelper,
    )
    {
    }

    public function getPromotion(string $slug): ?CategoryRoomMainData
    {
        $row = DB::table('promotions')
            ->leftJoin('photos', function ($join) {
                $join->on('promotions.id', '=', 'photos.imageable_id')
                    ->where('photos.model_type', '=', self::PROMOTION_MODEL_TYPE)
                    ->where('photos.type', '=', 'image');
            })
            ->where('promotions.slug', $slug)
            ->select(
                'promotions.*',
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

        $meta = json_decode($row->meta, true);
        return new CategoryRoomMainData(
            id: $row->id,
            name: $row->name,
            slug: $row->slug,
            children: [],
            entity: 'category',
            parent: null,
            totalProducts: 0,
            title: $meta['title'] ?? '',
            description: $meta['description'] ?? '',
            image: !empty($row->photo_id) ? $this->imageInfoHelper->build($row) : null,
        );
    }

    public function getProductIdsInPromotion(int $id): array
    {
        return DB::table('promotions_products')
            ->join('products', 'products.id', '=', 'promotions_products.product_id')
            ->where('promotions_products.promotion_id', $id)
            ->where('products.published', true)
            ->where('products.not_sale', false)
            ->pluck('promotions_products.product_id')
            ->toArray();
    }
}
