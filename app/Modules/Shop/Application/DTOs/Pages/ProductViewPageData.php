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

        public array $attributes = [],
        /** @var ProductCardData[] $equivalents */
        public array $equivalents = [], //Аналоги

        //TODO Блоки еще не реализованные
        public array $bonus = [],
        /** @var ProductCardData[] $series */
        public array $series = [], //Товары той же серии
        public array $reviews = [],
        /** @var ProductCardData[] $related */
        public array $related = [], //Связанные (акссесуары)
        public ?array $modification = null,

        //TODO Рекомендации и Еще чтото

    )
    {
    }
}
