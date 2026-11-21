<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Builders;

use App\Modules\Accounting\Application\Actions\Organization\GetSellerOrganizationQuery;
use App\Modules\Accounting\Application\DTOs\Organization\OrganizationSchemaData;
use App\Modules\Shop\Application\DTOs\Entities\CategoryRoomData;
use App\Modules\Shop\Application\DTOs\Entities\IkeaProductData;
use App\Modules\Shop\Application\DTOs\Entities\PageData;
use App\Modules\Shop\Application\DTOs\Entities\PostCardData;
use App\Modules\Shop\Application\DTOs\Entities\PostCategoryData;
use App\Modules\Shop\Application\DTOs\Entities\PostData;
use App\Modules\Shop\Application\DTOs\Entities\ProductData;
use App\Modules\Shop\Application\DTOs\PageElements\SeoData;
use App\Modules\Shop\Application\DTOs\Pages\CatalogIndexPageData;
use App\Modules\Shop\Application\DTOs\Pages\PostViewPageData;
use App\Modules\Shop\Application\Interfaces\BreadcrumbProviderInterface;
use App\Modules\Shop\Domain\Schema\BreadcrumbSchema;
use App\Modules\Shop\Domain\Schema\FAQSchema;
use App\Modules\Shop\Domain\Schema\ItemListSchema;
use App\Modules\Shop\Domain\Schema\OfferSchema;
use App\Modules\Shop\Domain\Schema\OrganizationSchema;
use App\Modules\Shop\Domain\Schema\ProductSchema;
use App\Modules\Shop\Domain\Schema\SchemaData;
use App\Modules\Shop\Domain\Schema\WebPageSchema;

class SchemaBuilder
{
    private ?OrganizationSchema $organization = null;
    private bool $organizationResolved = false;

    public function __construct(
        private readonly BreadcrumbProviderInterface $breadcrumbProvider,
        private readonly GetSellerOrganizationQuery $sellerOrganizationQuery,
    ) {}

    public function buildForProduct(ProductData $product, SeoData $seo): SchemaData
    {
        $graph = [];
        $graph[] = $this->webPage($seo);
        $this->appendOrganization($graph);
        $breadcrumbs = $this->breadcrumbProvider->generate('shop.product.view', [$product->slug]);
        $graph[] = new BreadcrumbSchema($breadcrumbs);
        $graph[] = new ProductSchema(
            name: $product->name,
            description: $product->description ?? '',
            image: !empty($product->images) ? url($product->images[0]->src) : '',
            sku: $product->code,
            offer: new OfferSchema($product->price),
            url: route('shop.product.view', $product->slug)
        );

        return new SchemaData($graph);
    }

    public function buildForIkeaProduct(IkeaProductData $product, SeoData $seo): SchemaData
    {
        $graph = [];
        $graph[] = $this->webPage($seo);
        $this->appendOrganization($graph);
        $breadcrumbs = $this->breadcrumbProvider->generate('shop.ikea.product', [$product->code]);
        $graph[] = new BreadcrumbSchema($breadcrumbs);
        $graph[] = new ProductSchema(
            name: $product->name,
            description: $product->description,
            image: !empty($product->images) ? url($product->images[0]->src) : '',
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
    public function buildForCategoryIndex(array $categories, string $entity, SeoData $seo): SchemaData
    {
        $graph = [];
        $graph[] = $this->webPage($seo);
        $this->appendOrganization($graph);
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

    public function buildForProductIndex(array $products, string $slug, string $entity, array $faq, SeoData $seo): SchemaData
    {
        $graph = [];
        $graph[] = $this->webPage($seo);
        $this->appendOrganization($graph);
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
        if (!empty($faq)) $graph[] = new FAQSchema($faq);

        return new SchemaData($graph);
    }

    public function buildForPage(PageData $data, SeoData $seo): SchemaData
    {
        $graph = [];
        $graph[] = $this->webPage($seo);
        $this->appendOrganization($graph);
        $breadcrumbs = $this->breadcrumbProvider->generate('shop.page.view', [$data->slug]);
        $graph[] = new BreadcrumbSchema($breadcrumbs);

        return new SchemaData($graph);
    }

    public function buildForPost(PostData $data, array $faq, SeoData $seo): SchemaData
    {
        $graph = [];
        $graph[] = $this->webPage($seo);
        $this->appendOrganization($graph);
        $breadcrumbs = $this->breadcrumbProvider->generate('shop.post.view', [$data->slug]);
        $graph[] = new BreadcrumbSchema($breadcrumbs);
        if (!empty($faq)) $graph[] = new FAQSchema($faq);

        return new SchemaData($graph);
    }

    public function createSchema(SeoData $seo): SchemaData
    {
        $graph = [];
        $graph[] = $this->webPage($seo);
        $this->appendOrganization($graph);

        return new SchemaData($graph);
    }

    /**
     *
     * @param PostCategoryData $category
     * @param PostCardData[] $posts
     * @return SchemaData
     */
    public function buildForPosts(PostCategoryData $category, array $posts, SeoData $seo): SchemaData
    {
        $graph = [];
        $graph[] = $this->webPage($seo);
        $this->appendOrganization($graph);
        $breadcrumbs = $this->breadcrumbProvider->generate('shop.posts.view', [$category->slug]);
        $graph[] = new BreadcrumbSchema($breadcrumbs);
        $items = array_map(
            fn(PostCardData $post) => [
                'name' => $post->caption,
                'url' => route('shop.post.view', $post->slug),
            ],
            $posts
        );
        $graph[] = new ItemListSchema($items);

        return new SchemaData($graph);
    }

    private function webPage(SeoData $seo): WebPageSchema
    {
        return new WebPageSchema(
            name: $seo->title,
            description: trim(strip_tags($seo->description)),
            url: url()->current(),
        );
    }

    /**
     * @param array<int, mixed> $graph
     */
    private function appendOrganization(array &$graph): void
    {
        $organization = $this->organization();
        if ($organization !== null) {
            $graph[] = $organization;
        }
    }

    private function organization(): ?OrganizationSchema
    {
        if (!$this->organizationResolved) {
            $this->organizationResolved = true;

            $data = $this->sellerOrganizationQuery->execute();
            if ($data !== null) {
                $this->organization = $this->buildOrganization($data);
            }
        }

        return $this->organization;
    }

    private function buildOrganization(OrganizationSchemaData $data): OrganizationSchema
    {
        return new OrganizationSchema(
            name: $data->name,
            url: config('app.url'),
            logoUrl: $data->logoUrl,
            logoWidth: $data->logoWidth,
            logoHeight: $data->logoHeight,
            logoCaption: $data->logoCaption,
            phones: $data->phones,
            address: $this->buildAddress($data),
            sameAs: $data->sameAs,
        );
    }

    /**
     * @return array{postalCode: ?string, addressLocality: ?string, streetAddress: string}
     */
    private function buildAddress(OrganizationSchemaData $data): array
    {
        $address = $data->address;

        $streetAddress = trim(implode(' ', array_filter([
            $address->street,
            $address->house,
            $address->building ? 'корпус ' . $address->building : null,
        ])));

        return [
            'postalCode' => $address->postalCode,
            'addressLocality' => $address->city,
            'streetAddress' => $streetAddress,
        ];
    }
}
