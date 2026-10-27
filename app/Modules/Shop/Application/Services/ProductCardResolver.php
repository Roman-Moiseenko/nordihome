<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Services;

use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\DTOs\Entities\ProductCardData;
use App\Modules\Shop\Application\DTOs\Entities\ProductModificationData;
use App\Modules\Shop\Infrastructure\Persistence\Query\ProductIndexQueryRepository;

/**
 * Загружает карточки товаров из сырых данных и применяет
 * региональный коэффициент цены.
 */
readonly class ProductCardResolver
{
    public function __construct(
        private ProductIndexQueryRepository $productIndexQueryRepository,
        private RegionalPriceCalculator $regionalPriceCalculator,
    )
    {
    }

    /**
     * @param int[] $productIds
     * @return ProductCardData[]
     */
    public function resolveCards(array $productIds, ClientContext $clientContext): array
    {
        if (empty($productIds)) {
            return [];
        }

        $raw = $this->productIndexQueryRepository->loadProductCards($productIds, $clientContext);

        return array_map(
            fn(array $item) => $this->regionalPriceCalculator->productCardData(
                ProductCardData::fromArray($item),
                $clientContext->region
            ),
            $raw
        );
    }

    /**
     * @param int[] $productIds
     * @param array<int, string> $variantNamesByProduct
     * @param array<int, ?string> $variantImagesByProduct
     * @return ProductModificationData[]
     */
    public function resolveModificationCards(
        array $productIds,
        ClientContext $clientContext,
        array $variantNamesByProduct = [],
        array $variantImagesByProduct = [],
    ): array
    {
        if (empty($productIds)) {
            return [];
        }

        $cards = $this->resolveCards($productIds, $clientContext);

        return array_map(
            fn(ProductCardData $card) => ProductModificationData::fromProductCardData(
                $card,
                $variantNamesByProduct[$card->id] ?? null,
                $variantImagesByProduct[$card->id] ?? null,
            ),
            $cards
        );
    }
}
