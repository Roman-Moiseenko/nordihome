<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Services;

use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\DTOs\Entities\ProductModificationData;
use App\Modules\Shop\Infrastructure\Persistence\Query\ModificationGroupQueryRepository;

/**
 * Сворачивает список товаров до базовых товаров модификаций (is_primary = true)
 * и подготавливает массив товаров-вариантов (базовый идёт первым).
 */
readonly class ProductModificationResolver
{
    public function __construct(
        private ModificationGroupQueryRepository $repository,
        private ProductCardResolver $productCardResolver,
    )
    {
    }

    /**
     * Сворачивает список товаров до базовых (is_primary = true) и возвращает
     * карту ID вариантов по базовому товару. Карточки вариантов загружаются
     * отдельно в ProductListingResolver одним запросом для всех ID.
     *
     * @param int[] $matchedIds  отфильтрованные и отсортированные ID товаров
     * @param int[] $listingIds  все ID товаров списка (до фильтрации)
     * @return array{
     *     displayIds: int[],
     *     variantIdsByPrimary: array<int, int[]>,
     *     variantNamesByProduct: array<int, string>,
     *     variantImagesByProduct: array<int, ?string>
     * }
     */
    public function resolve(array $matchedIds, array $listingIds, ClientContext $clientContext): array
    {
        if (empty($matchedIds)) {
            return [
                'displayIds' => [],
                'variantIdsByPrimary' => [],
                'variantNamesByProduct' => [],
                'variantImagesByProduct' => [],
            ];
        }

        $groups = $this->repository->resolveGroups($listingIds);
        $primaryByProduct = $groups['primaryByProduct'];

        $displayIds = [];
        $seen = [];
        foreach ($matchedIds as $id) {
            $primaryId = $primaryByProduct[$id] ?? $id;

            if (isset($seen[$primaryId])) {
                continue;
            }

            $seen[$primaryId] = true;
            $displayIds[] = $primaryId;
        }

        return [
            'displayIds' => $displayIds,
            'variantIdsByPrimary' => $groups['variantIdsByPrimary'],
            'variantNamesByProduct' => $groups['variantNamesByProduct'],
            'variantImagesByProduct' => $groups['variantImagesByProduct'],
        ];
    }

    /**
     * Список товаров-вариантов модификации для карточки конкретного товара.
     * Содержит все товары модификации, базовый (is_primary = true) идёт первым.
     *
     * @return ProductModificationData[]
     */
    public function resolveForProduct(int $productId, ClientContext $clientContext): array
    {
        $groups = $this->repository->resolveGroups([$productId]);

        $primaryId = $groups['primaryByProduct'][$productId] ?? $productId;
        $variantIds = $groups['variantIdsByPrimary'][$primaryId] ?? [];

        if (empty($variantIds)) {
            return [];
        }

        return $this->productCardResolver->resolveModificationCards(
            $variantIds,
            $clientContext,
            $groups['variantNamesByProduct'],
            $groups['variantImagesByProduct'],
        );
    }
}
