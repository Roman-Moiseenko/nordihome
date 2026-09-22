<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomMainData;
use App\Modules\Shop\Application\Helpers\ImageInfoDataHelper;
use Illuminate\Support\Facades\DB;

class GroupPageQueryRepository
{
    private const string GROUP_MODEL_TYPE = 'catalog.group';

    public function __construct(
        private readonly ImageInfoDataHelper $imageInfoHelper,
    )
    {
    }


    public function getGroup(string $slug): ?CategoryRoomMainData
    {
        $row = DB::table('groups')
            ->leftJoin('photos', function ($join) {
                $join->on('groups.id', '=', 'photos.imageable_id')
                    ->where('photos.model_type', '=', self::GROUP_MODEL_TYPE)
                    ->where('photos.type', '=', 'image');
            })
            ->where('groups.slug', $slug)
            ->where('published', true)
            ->select(
                'groups.*',
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

    public function getProductIdsInGroup(int $id): array
    {
        return DB::table('groups_products')
            ->join('products', 'products.id', '=', 'groups_products.product_id')
            ->where('groups_products.group_id', $id)
            ->where('products.published', true)
            ->where('products.not_sale', false)
            ->pluck('groups_products.product_id')
            ->toArray();
    }
}
