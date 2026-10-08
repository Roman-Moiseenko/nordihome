<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\DTOs\Product\EditPage\UpdateEquivalentProductData;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Catalog\Domain\Interfaces\EquivalentProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;

readonly class SetEquivalentProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private EquivalentProductRepositoryInterface $equivalentProductRepository,
    ) {
    }

    public function execute(int $id, UpdateEquivalentProductData $dto): ProductEntity
    {
        $currentIds = $this->equivalentProductRepository->getEquivalentIdsByProductId($id);
        $currentId = $currentIds[0] ?? null;

        $equivalentId = $dto->equivalentId;

        if ($equivalentId === null || $equivalentId === 0) {
            if ($currentId !== null) {
                $this->equivalentProductRepository->detachProducts((int) $currentId, [$id]);
            }
        } else {
            if ($currentId === null) {
                $this->equivalentProductRepository->attachProducts($equivalentId, [$id]);
            } elseif ((int) $currentId !== $equivalentId) {
                $this->equivalentProductRepository->detachProducts((int) $currentId, [$id]);
                $this->equivalentProductRepository->attachProducts($equivalentId, [$id]);
            }
        }

        return $this->productRepository->getById($id);
    }
}
