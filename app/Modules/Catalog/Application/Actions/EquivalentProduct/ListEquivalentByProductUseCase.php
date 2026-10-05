<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\EquivalentProduct;

use App\Modules\Catalog\Application\DTOs\Equivalent\EquivalentViewData;
use App\Modules\Catalog\Domain\Interfaces\EquivalentProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\EquivalentRepositoryInterface;

readonly class ListEquivalentByProductUseCase
{
    public function __construct(
        private EquivalentProductRepositoryInterface $equivalentProductRepository,
        private EquivalentRepositoryInterface $equivalentRepository,
    )
    {
    }

    /**
     * @return EquivalentViewData[]
     */
    public function execute(int $productId): array
    {
        $equivalentIds = $this->equivalentProductRepository->getEquivalentIdsByProductId($productId);

        if (empty($equivalentIds)) {
            return [];
        }

        $equivalents = $this->equivalentRepository->findByIds($equivalentIds);

        return array_map(
            fn($equivalent) => EquivalentViewData::fromEntity($equivalent),
            $equivalents
        );
    }
}
