<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Equivalent;

use App\Modules\Catalog\Domain\Entities\EquivalentEntity;
use App\Modules\Catalog\Domain\Interfaces\EquivalentRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ViewEquivalentQuery
{
    public function __construct(
        private EquivalentRepositoryInterface $equivalentRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): EquivalentEntity
    {
        if (!$userPermission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        return $this->equivalentRepository->getById($id);
    }
}
