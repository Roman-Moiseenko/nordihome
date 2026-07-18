<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Queries\Post;

use App\Modules\Shop\Application\DTOs\Pages\PostViewPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Query\ContentBlockQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\PostViewQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\SeoAdapter;

readonly class PostPageQuery
{
    public function __construct(
        private PostViewQueryRepository       $postRepository,
        private ContentBlockQueryRepository   $blockRepository,
        private SeoAdapter                    $seoAdapter,
        private SchemaBuilder                 $schemaBuilder,
    )
    {
    }

    public function execute(string $slug): PostViewPageData
    {
        // 1. Получить PostData (один SQL-запрос с фото)
        $post = $this->postRepository->getPostBySlug($slug);

        // 2. Получить все контент-блоки для поста (один SQL-запрос, сортировка по sort_order)
        $blocks = $this->blockRepository->getBlocksByContainer('post', $post->id);

        // 3. SEO
        $meta = $this->seoAdapter->getSeo('content.post', $post);

        // 4. Schema
        $schema = $this->schemaBuilder->buildForPost($post);

        return new PostViewPageData(
            post: $post,
            blocks: $blocks,
            meta: $meta,
            schema: $schema,
        );
    }
}
