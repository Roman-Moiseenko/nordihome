<?php

declare(strict_types=1);

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shop\Application\DTOs\Entities\PostData;
use Illuminate\Support\Facades\DB;

class PostViewQueryRepository
{
    private const string PHOTO_MODEL_TYPE = 'content.post';

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
                'photos.file as image_file',
            )
            ->first();

        if (!$row) {
            throw new \DomainException("Post not found by slug: {$slug}");
        }

        $meta = json_decode($row->meta ?? '{}', true);

        $image = '';
        if (!empty($row->image_file)) {
            $image = '/uploads/content/post/' . $row->id . '/' . $row->image_file;
        }

        return new PostData(
            id: (int)$row->id,
            slug: $row->slug,
            title: $meta['title'] ?? $row->name,
            description: $meta['description'] ?? '',
            caption: $row->caption ?? '',
            fragment: $row->fragment ?? '',
            image: $image,
        );
    }
}
