<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shop\Application\DTOs\Entities\PostCardData;
use App\Modules\Shop\Application\DTOs\Entities\PostCategoryData;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PostIndexQueryRepository
{

    private const string PHOTO_MODEL_TYPE = 'content.post';

    public function getCategory(string $slug): PostCategoryData
    {
        $row = DB::table('post_categories')
            ->where('slug', $slug)
            ->select('id', 'slug', 'title', 'description')
            ->first();

        if (!$row) abort(404);

        return new PostCategoryData(
            id: (int)$row->id,
            slug: $row->slug,
            title: $row->title ?? '',
            description: $row->description ?? '',
        );
    }

    public function getPosts(int $id, $page, $perPage): LengthAwarePaginator
    {
        $query = DB::table('posts')
            ->where('posts.category_id', $id)
            ->where('posts.published', true)
            ->orderByDesc('posts.published_at')
            ->select(
                'posts.id',
                'posts.slug',
                'posts.caption',
                'posts.fragment',
                DB::raw("(SELECT file FROM photos WHERE imageable_id = posts.id AND model_type = '" . self::PHOTO_MODEL_TYPE . "' AND type = 'image' LIMIT 1) as image_file")
            );

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $postCards = $paginator->getCollection()->map(function ($item) {
            $image = '';
            if (!empty($item->image_file)) {
                $image = '/uploads/content/post/' . $item->id . '/' . $item->image_file;
            }

            return new PostCardData(
                id: (int)$item->id,
                slug: $item->slug,
                caption: $item->caption ?? '',
                fragment: $item->fragment ?? '',
                image: $image,
            );
        });

        return new LengthAwarePaginator(
            items: $postCards,
            total: $paginator->total(),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => request()->url(),
                'query' => request()->query(),
            ],
        );
    }
}
