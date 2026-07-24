<?php

namespace App\Modules\Shop\Application\DTOs\Pages;

use App\Modules\Content\Entity\PostCategory;
use App\Modules\Shop\Application\DTOs\Entities\PostCardData;
use App\Modules\Shop\Application\DTOs\Entities\PostCategoryData;
use App\Modules\Shop\Application\DTOs\Entities\PostData;
use App\Modules\Shop\Application\DTOs\PageElements\PaginatorData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Domain\Schema\SchemaData;

class PostIndexPageData
{
    public function __construct(

        public PostCategoryData $category,
        /** @var PostCardData[] $posts */
        public array $posts,
        public PaginatorData        $paginator,
        public SeoData     $meta,
        public SchemaData  $schema,

    )
    {
    }
}
