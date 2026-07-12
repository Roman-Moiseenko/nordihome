<?php

namespace App\Modules\Shop\Application\Queries\Ikea;

use App\Modules\Setting\Entity\Settings;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Application\DTOs\Pages\IkeaIndexPageData;
use App\Modules\Shop\Application\DTOs\Pages\IkeaProductPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;

class IkeaProductQuery
{
    public function __construct(
        private Settings      $settings,
        private SchemaBuilder $schemaBuilder,
    )
    {
    }

    public function execute(string $slug): IkeaProductPageData
    {
        $web = $this->settings->web;



        //FIXME
        $schema = $this->schemaBuilder->createSchema();

        return new IkeaProductPageData(
            meta: new SeoData(
                title: $web->ikea_title,
                description: $web->ikea_desc,
            ),
            schema: $schema,

        );
    }
}
