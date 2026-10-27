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
     * @param int[] $matchedIds  отфильтрованные и отсортированные ID товаров
     * @param int[] $listingIds  все ID товаров списка (до фильтрации)
     * @return array{displayIds: int[], variantsByPrimary: array<int, ProductModificationData[]>}
     */
    public function resolve(array $matchedIds, array $listingIds, ClientContext $clientContext): array
    {
        if (empty($matchedIds)) {
            return ['displayIds' => [], 'variantsByPrimary' => []];
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

        $variantsByPrimary = [];
        foreach ($displayIds as $primaryId) {
            $variantIds = $groups['variantIdsByPrimary'][$primaryId] ?? [];
            if (empty($variantIds)) {
                continue;
            }

            $variantsByPrimary[$primaryId] = $this->productCardResolver->resolveModificationCards(
                $variantIds,
                $clientContext,
                $groups['variantNamesByProduct'],
                $groups['variantImagesByProduct'],
            );
        }

        return [
            'displayIds' => $displayIds,
            'variantsByPrimary' => $variantsByPrimary,
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
