<?php

namespace App\Modules\Shop\Application\DTOs\Pages;

use App\Modules\Shop\Application\DTOs\Entities\ProductData;
use App\Modules\Shop\Application\DTOs\PageElements\SchemaData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;

class ProductViewPageData
{
    public function __construct(

        public ProductData         $product,
        public SeoData                $meta,
        public SchemaData             $schema,


        //TODO Рекомендации
        // public ...
        //TODO Еще чтото
        // public ...

        public ?array $bonus = null,
        public ?array $series = null,
        public ?array $equivalents = null,
        public ?array $reviews = null,
        public ?array $attributes = null,

        public ?array $related = null,
        public ?array $modification = null,



    )
    {
    }
}
