<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Services;

use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Content\Application\DTOs\ProductWidget\ProductSearchResultData;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Shared\Application\Interfaces\PhotoRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\PhotoType;

final readonly class ProductSearchService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private PhotoRepositoryInterface $photoRepository,
    ) {}

    private const string MODEL_TYPE = 'catalog.product';
    private const string PHOTO_TYPE = 'gallery';

    /**
     * Поиск товаров с подгрузкой изображений.
     *
     * @param string $query
     * @param int $limit
     * @return ProductSearchResultData[]
     */
    public function search(string $query, int $limit = 10): array
    {
        $products = $this->productRepository->search($query, $limit);

        if (empty($products))
            return [];


        $productIds = array_map(fn(ProductEntity $p) => $p->id, $products);
        $images = $this->loadImages($productIds);

        return array_map(
            fn(ProductEntity $product) => $this->toResultData($product, $images[$product->id] ?? []),
            $products,
        );
    }


    /**
     * @param int[] $productIds
     * @return array<int, array{src: ?string, alt: ?string, next_src: ?string, next_alt: ?string}>
     */
    private function loadImages(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        $type = new PhotoType(self::PHOTO_TYPE);
        $gallery = $this->photoRepository->findByEntities($productIds, self::MODEL_TYPE, $type);
        $result = [];
        foreach ($productIds as $id) {
            $photos = $gallery[$id] ?? [];
            $first = $photos[0] ?? null;
            $second = $photos[1] ?? null;

            $result[$id] = [
                'src' => $first?->uploadUrl,
                'alt' => $first?->alt,
                'next_src' => $second?->uploadUrl,
                'next_alt' => $second?->alt,
            ];
        }

        return $result;
    }

    private function toResultData(ProductEntity $product, array $images): ProductSearchResultData
    {
        return new ProductSearchResultData(
            id: $product->id,
            name: $product->name,
            code: $product->code->getCode(),
            code_search: $product->code->getCodeSearch(),
            url: route('admin.catalog.product.edit', $product->id),
            short: $product->short,
            price: null, // цена будет добавлена позже
            quantity: 1,
            in_stock: true,
            image_src: $images['src'] ?? null,
            image_alt: $images['alt'] ?? null,
            image_next_src: $images['next_src'] ?? null,
            image_next_alt: $images['next_alt'] ?? null,
        );
    }
}
