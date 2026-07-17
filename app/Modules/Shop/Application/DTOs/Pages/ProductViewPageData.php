<?php

namespace App\Modules\Shop\Application\DTOs\Pages;

use App\Modules\Shop\Application\DTOs\Entities\ProductCardData;
use App\Modules\Shop\Application\DTOs\Entities\ProductData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Domain\Schema\SchemaData;

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
        /** @var ProductCardData[] $series */
        public ?array $series = null, //Товары той же серии
        /** @var ProductCardData[] $equivalents */
        public ?array $equivalents = null, //Аналоги
        public ?array $reviews = null,

        public ?array $attributes = null,
        /** @var ProductCardData[] $related */
        public ?array $related = null, //Связанные (акссесуары)
        public ?array $modification = null,



    )
    {
    }
}
