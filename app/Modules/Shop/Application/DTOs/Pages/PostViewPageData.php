<?php

namespace App\Modules\Shop\Application\DTOs\Pages;

use App\Modules\Shop\Application\DTOs\Entities\PostData;
use App\Modules\Shop\Application\DTOs\PageElements\ContentBlockPageData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Domain\Schema\SchemaData;

class PostViewPageData
{

    public function __construct(

        public PostData $post,
        /** @var ContentBlockPageData $blocks */
        public array $blocks,

        public SeoData     $meta,
        public SchemaData  $schema,

    )
    {
    }
}
