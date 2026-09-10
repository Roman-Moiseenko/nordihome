<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shared\Application\Actions\GetImageThumbByRowUseCase;
use App\Modules\Shared\Infrastructure\Services\PhotoService;
use App\Modules\Shop\Application\DTOs\Elements\IdNameData;
use App\Modules\Shop\Application\DTOs\Entities\PostCardData;
use App\Modules\Shop\Application\DTOs\Entities\PostCategoryData;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PostIndexQueryRepository
{

    private const string MODEL_TYPE = 'content.post';
    public function __construct(
        private readonly GetImageThumbByRowUseCase $imageThumbUseCase,
    )
    {
    }
    public function getCategory(string $slug): PostCategoryData
    {
        $row = DB::table('post_categories')
            ->where('slug', $slug)
            ->select('id', 'slug','meta', 'title', 'description')
            ->first();

        if (!$row) abort(404);
        $meta = json_decode($row->meta, true);
        return new PostCategoryData(
            id: (int)$row->id,
            slug: $row->slug,
            caption: $row->title ?? '',
            title: $meta['title'] ?? '',
            description: $meta['description'] ?? '',
        );
    }

    public function getPosts(int $id, $page, $perPage, array $filters): LengthAwarePaginator
    {
        $query = DB::table('posts');

        if (isset($filters['label_id'])) {
            $query->whereExists(function ($sub) use ($filters) {
                $sub->select(DB::raw(1))
                    ->from('labels_posts')
                    ->whereColumn('labels_posts.post_id', 'posts.id')
                    ->where('labels_posts.label_id', (int)$filters['label_id']);
            });
        }

        $query->where('posts.category_id', $id)
            ->where('posts.published', true)
            ->leftJoin('photos', function ($join) {
                $join->on('posts.id', '=', 'photos.imageable_id')
                    ->where('photos.model_type', '=', self::MODEL_TYPE)
                    ->where('photos.type', '=', 'image');
            })
            ->orderByDesc('posts.published_at')
            ->select(
                'posts.id',
                'posts.slug',
                'posts.caption',
                'posts.fragment',
                'photos.id as photo_id',
                'photos.file as photo_file',
                'photos.model_type as model_type',
            );

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $postCards = $paginator->getCollection()->map(function ($item) {


            return new PostCardData(
                id: (int)$item->id,
                slug: $item->slug,
                caption: $item->caption ?? '',
                fragment: $item->fragment ?? '',
                image: $this->imageThumbUseCase->execute($item),
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

    /**
     * Получить метки, у которых есть посты в указанной категории.
     *
     * @param int $categoryId
     * @return IdNameData[]
     */
    public function getLabels(int $categoryId): array
    {
        return DB::table('labels')
            ->join('labels_posts', 'labels.id', '=', 'labels_posts.label_id')
            ->join('posts', 'posts.id', '=', 'labels_posts.post_id')
            ->where('posts.category_id', $categoryId)
            ->where('posts.published', true)
            ->select('labels.id', 'labels.name')
            ->distinct()
            ->orderBy('labels.name')
            ->get()
            ->map(fn($row) => new IdNameData(
                id: (int)$row->id,
                name: $row->name,
            ))
            ->all();
    }

}
