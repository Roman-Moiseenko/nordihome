<?php

namespace App\Modules\Shop\Application\Queries\Post;

use App\Modules\Setting\Application\Actions\GetWebSettingsUseCase;
use App\Modules\Shop\Application\DTOs\PageElements\FilterPostsData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Application\DTOs\Pages\PostIndexPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\PaginatorBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Query\PostIndexQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\SeoAdapter;

readonly class PostIndexQuery
{

    public function __construct(

        private PostIndexQueryRepository $repository,
        private PaginatorBuilder         $paginatorBuilder,
        //private SeoAdapter                    $seoAdapter,
        private SchemaBuilder            $schemaBuilder,
        private GetWebSettingsUseCase $webSettingsUseCase,
    )
    {
    }

    public function execute(string $slug, array $params): PostIndexPageData
    {
        $web = $this->webSettingsUseCase->execute();
        $perPage = 20;
        $page = (int)($params['page'] ?? 1);

        //1. Находим категорию постов
        $category = $this->repository->getCategory($slug);

        //2. Загружаем с пагинацией записи постов

        $postsPaginator = $this->repository->getPosts($category->id, $page, $perPage, $params);

        $schema = $this->schemaBuilder->buildForPosts(
            $category, $postsPaginator->items()
        );

        $labels = $this->repository->getLabels($category->id);

        $filters = new FilterPostsData(
            labels: $labels,
            labelId: isset($params['label_id']) ? (int)$params['label_id'] : null,
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
        $meta = new SeoData($category->title, $category->description);
        if ($page > 1) $meta->title .= ' - Страница ' . $page;
        $meta->canonical = route('shop.posts.view', $slug);
        $meta->ogSiteName = $web->web_name;

        return new PostIndexPageData(
            category: $category,
            posts: $postsPaginator->items(),
            paginator: $paginator,
            meta: $meta->asArticle(),
            schema: $schema,
            filters: $filters,
        );
    }
}
