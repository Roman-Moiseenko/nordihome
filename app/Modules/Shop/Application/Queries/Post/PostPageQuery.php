<?php

namespace App\Modules\Shop\Application\Queries\Post;

use App\Modules\Shop\Application\DTOs\Entities\PostData;
use App\Modules\Shop\Application\DTOs\Pages\PostViewPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\SeoAdapter;

readonly class PostPageQuery
{
    public function __construct(
        private SeoAdapter                  $seoAdapter,
        private SchemaBuilder               $schemaBuilder,
    )
    {
    }
    public function execute(string $slug): ?PostViewPageData
    {
        // Получить PostData
        //Получить все блоки для PostEntity отсортированные

        //Формируем схему
        //Формируем заголовки для SEO
        return new PostViewPageData(

        );

    }

}
