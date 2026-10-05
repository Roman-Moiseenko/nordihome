<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Equivalent;

use App\Modules\Catalog\Application\DTOs\Equivalent\EquivalentUpdateData;
use App\Modules\Catalog\Domain\Entities\EquivalentEntity;
use App\Modules\Catalog\Domain\Interfaces\EquivalentRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateEquivalentUseCase
{
    public function __construct(
        private EquivalentRepositoryInterface $equivalentRepository,
    )
    {
    }

    public function execute(int $id, EquivalentUpdateData $dto, UserPermission $userPermission): EquivalentEntity
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        $equivalent = $this->equivalentRepository->getById($id);

        $equivalent->name = trim($dto->name);

        if ($dto->categoryId !== null) {
            $equivalent->categoryId = $dto->categoryId;
        }

        return $this->equivalentRepository->save($equivalent);
    }
}
