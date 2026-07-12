<?php

namespace App\Modules\Shop\Application\Queries\Product;

use App\Modules\Auth\Infrastructure\Models\Client;
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

        $product = $this->repository->getProductBySlug($slug);





        $meta = $this->seoAdapter->getSeoFromProductInfo($product);
        //FIXME
        $schema = $this->schemaBuilder->createSchema();

        return new ProductViewPageData(
            product: $product,
            meta: new SeoData($meta->title, $meta->description),
            schema: $schema,
        );
    }
}
