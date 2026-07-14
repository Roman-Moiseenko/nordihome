<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Builders;

use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomData;
use App\Modules\Shop\Application\DTOs\Entities\IkeaProductData;
use App\Modules\Shop\Application\DTOs\Entities\ProductData;
use App\Modules\Shop\Application\DTOs\Pages\CatalogIndexPageData;
use App\Modules\Shop\Application\Interfaces\BreadcrumbProviderInterface;
use App\Modules\Shop\Domain\Schema\BreadcrumbSchema;
use App\Modules\Shop\Domain\Schema\ItemListSchema;
use App\Modules\Shop\Domain\Schema\OfferSchema;
use App\Modules\Shop\Domain\Schema\OrganizationSchema;
use App\Modules\Shop\Domain\Schema\ProductSchema;
use App\Modules\Shop\Domain\Schema\SchemaData;
use App\Modules\Shop\Domain\Schema\WebPageSchema;

class SchemaBuilder
{
    private OrganizationSchema $organization;

    public function __construct(private readonly BreadcrumbProviderInterface $breadcrumbProvider)
    {
        $this->organization = new OrganizationSchema(
            name: config('app.name'),
            url: config('app.url'),
            logoUrl: asset('images/logo.png'),
            sameAs: [
                'https://vk.com/...',
                'https://t.me/...',
            ]
        );
    }

    public function buildForProduct(ProductData $product): SchemaData
    {
        $graph = [];
        $graph[] = $this->organization;
        $breadcrumbs = $this->breadcrumbProvider->generate('shop.product.view', [$product->slug]);

        $graph[] = new BreadcrumbSchema($breadcrumbs);

        $graph[] = new ProductSchema(
            name: $product->name,
            description: $product->description ?? '',
            image: url($product->images[0]->src) ?? '',
            sku: $product->code,
            offer: new OfferSchema($product->price),
            url: route('shop.product.view', $product->slug)
        );

        return new SchemaData($graph);
    }

    public function buildForIkeaProduct(IkeaProductData $product): SchemaData
    {
        $graph = [];
        $graph[] = $this->organization;
        $breadcrumbs = $this->breadcrumbProvider->generate('shop.ikea.product', [$product->code]);

        $graph[] = new BreadcrumbSchema($breadcrumbs);

        $graph[] = new ProductSchema(
            name: $product->name,
            description: $product->description,
            image: url($product->images[0]->src) ?? '',
            sku: $product->code,
            offer: new OfferSchema($product->price),
            url: route('shop.ikea.product', $product->code)
        );

        return new SchemaData($graph);
    }

    /**
     * @param CategoryRoomData[] $categories
     * @return SchemaData
     */
    public function buildForCategoryIndex(array $categories, string $entity): SchemaData
    {
        $graph = [];
        $graph[] = $this->organization;

        $breadcrumbs = $this->breadcrumbProvider->generate('shop.' . $entity . '.index');

        $graph[] = new BreadcrumbSchema($breadcrumbs);

        // Корневая категория – список дочерних
        $items = array_map(
            fn($child) => [
                'name' => $child->name,
                'url' => route('shop.' . $entity . '.view', $child->slug),
            ],
            $categories
        );
        $graph[] = new ItemListSchema($items);


        return new SchemaData($graph);
    }

    public function buildForProductIndex(array $products, string $slug, string $entity): SchemaData
    {
        $graph = [];
        $graph[] = $this->organization;

        $breadcrumbs = $this->breadcrumbProvider->generate('shop.' . $entity . '.view', [$slug]);
        $graph[] = new BreadcrumbSchema($breadcrumbs);
        // Категория с товарами – список товаров
        $items = array_map(
            fn($product) => [
                'name' => $product->name,
                'url' => route('shop.product.view', $product->slug),
            ],
            $products
        );
        $graph[] = new ItemListSchema($items);
        return new SchemaData($graph);
    }


    public function buildForPage(PageData $data): SchemaData
    {
        $graph = [];
        $graph[] = $this->organization;
        $graph[] = new BreadcrumbSchema($data->breadcrumbs);
        $graph[] = new WebPageSchema(
            name: $data->meta->title,
            description: $data->meta->description,
            url: url()->current()
        );
        return new SchemaData($graph);
    }

    public function createSchema(): SchemaData
    {
        return new SchemaData();
    }
}
