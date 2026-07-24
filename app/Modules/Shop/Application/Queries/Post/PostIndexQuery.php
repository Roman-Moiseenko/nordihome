<?php

namespace App\Modules\Shop\Application\Queries\Post;

use App\Modules\Content\Infrastructure\Models\Post;
use App\Modules\Shop\Application\DTOs\Entities\PostData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Application\DTOs\Pages\PostIndexPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\PaginatorBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Query\PostIndexQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\SeoAdapter;

class PostIndexQuery
{

    public function __construct(

        private PostIndexQueryRepository $repository,
        private PaginatorBuilder            $paginatorBuilder,
        //private SeoAdapter                    $seoAdapter,
        private SchemaBuilder                 $schemaBuilder,
    )
    {
    }
    public function execute(string $slug): PostIndexPageData
    {
        $perPage = 20;
        $page = (int)($params['page'] ?? 1);

        //1. Находим категорию постов
        $category = $this->repository->getCategory($slug);

        //2. Загружаем с пагинацией записи постов

        $postsPaginator = $this->repository->getPosts($category->id, $page, $perPage);

        $schema = $this->schemaBuilder->buildForPosts(
            $category, $postsPaginator->items()
        );

        $paginator = $this->paginatorBuilder->build(
            total: $postsPaginator->total(),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => '/' . request()->path(),
                'query' => array_diff_key(request()->query(), ['page' => null]),
            ]
        );

        return new PostIndexPageData(
            category: $category,
            posts: $postsPaginator->items(),
            paginator: $paginator,
            meta: new SeoData('', ''),
            schema: $schema,
        );
    }
}
