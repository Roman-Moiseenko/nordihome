<?php

declare(strict_types=1);

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shop\Application\DTOs\Entities\PostData;
use App\Modules\Shop\Application\Helpers\ImageInfoDataHelper;
use Illuminate\Support\Facades\DB;

class PostViewQueryRepository
{
    private const string PHOTO_MODEL_TYPE = 'content.post';

    public function __construct(
        private readonly ImageInfoDataHelper $imageInfoHelper,
    )
    {
    }

    public function getPostBySlug(string $slug): PostData
    {
        $row = DB::table('posts')
            ->where('posts.slug', $slug)
            ->where('posts.published', true)
            ->leftJoin('photos', function ($join) {
                $join->on('posts.id', '=', 'photos.imageable_id')
                    ->where('photos.model_type', '=', self::PHOTO_MODEL_TYPE)
                    ->where('photos.type', '=', 'image');
            })
            ->select(
                'posts.id',
                'posts.name',
                'posts.slug',
                'posts.caption',
                'posts.fragment',
                'posts.meta',
                'posts.published_at',
                'posts.updated_at',
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

        if (!$row) {
            throw new \DomainException("Post not found by slug: {$slug}");
        }

        $meta = json_decode($row->meta ?? '{}', true);

        return new PostData(
            id: (int)$row->id,
            slug: $row->slug,
            title: $meta['title'] ?? $row->name,
            description: $meta['description'] ?? '',
            caption: $row->caption ?? '',
            fragment: $row->fragment ?? '',
            image: $this->imageInfoHelper->build($row),
            publishedAt: $this->toIso8601($row->published_at ?? null),
            updatedAt: $this->toIso8601($row->updated_at ?? null),
        );
    }

    private function toIso8601(?string $value): string
    {
        return $value ? \Carbon\Carbon::parse($value)->toIso8601String() : '';
    }
}
