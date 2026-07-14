<?php

namespace App\Modules\Shop\Application\Queries\Product;

use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Catalog\Domain\ValueObjects\PriceType;
use App\Modules\Shop\Application\DTOs\Entities\ProductData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Application\DTOs\Pages\ProductViewPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Query\ProductViewQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\SeoAdapter;

readonly class ProductViewQuery
{

    public function __construct(
        private ProductViewQueryRepository $repository,
        private SchemaBuilder               $schemaBuilder,
        private SeoAdapter                  $seoAdapter,

    )
    {
    }
    public function execute(string $slug, ?Client $client): ProductViewPageData
    {
        //TODO по клиенту $client получаем тип цены
        $product = $this->repository->getProductBySlug($slug, PriceType::RETAIL);

        $attributes = $this->repository->getAttributes($product);

        $meta = $this->seoAdapter->getSeo('catalog.product', $product);
        $schema = $this->schemaBuilder->buildForProduct($product);

        return new ProductViewPageData(
            product: $product,
            meta: $meta,
            schema: $schema,
            attributes: $attributes,
        );
    }
}
