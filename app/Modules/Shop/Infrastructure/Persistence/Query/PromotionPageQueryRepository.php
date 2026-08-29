<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shared\Infrastructure\Services\PhotoService;
use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomMainData;
use Illuminate\Support\Facades\DB;

class PromotionPageQueryRepository
{
    private const string PHOTO_MODEL_TYPE = 'catalog.product';

    public function __construct(
        private readonly PhotoService $photoService,
        private readonly AttributeQueryRepository $attributeQueryRepository,
    )
    {
    }

    public function getPromotion(string $slug): ?CategoryRoomMainData
    {
        $row = DB::table('promotions')
            ->where('promotions.slug', $slug)
            ->select('*')
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
