<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\EquivalentProduct;

use App\Modules\Catalog\Domain\Interfaces\EquivalentProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class DetachEquivalentsFromProductUseCase
{
    public function __construct(
        private EquivalentProductRepositoryInterface $equivalentProductRepository,
    )
    {
    }

    /**
     * Отвязать группы аналогов от товара.
     *
     * @param int   $productId
     * @param int[] $equivalentIds
     * @param UserPermission $userPermission
     */
    public function execute(int $productId, array $equivalentIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        $this->equivalentProductRepository->detachEquivalents($productId, $equivalentIds);
    }
}
