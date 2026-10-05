<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Equivalent;

use App\Modules\Catalog\Domain\Interfaces\EquivalentProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\EquivalentRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemoveEquivalentUseCase
{
    public function __construct(
        private EquivalentRepositoryInterface $equivalentRepository,
        private EquivalentProductRepositoryInterface $equivalentProductRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.delete')) {
            throw new AccessDeniedException();
        }

        $this->equivalentProductRepository->detachAllProducts($id);

        $this->equivalentRepository->delete($id);
    }
}
