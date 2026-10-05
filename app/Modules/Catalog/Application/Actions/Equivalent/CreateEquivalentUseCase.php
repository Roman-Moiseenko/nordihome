<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Equivalent;

use App\Modules\Catalog\Application\DTOs\Equivalent\EquivalentCreateData;
use App\Modules\Catalog\Domain\Entities\EquivalentEntity;
use App\Modules\Catalog\Domain\Interfaces\EquivalentRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CreateEquivalentUseCase
{
    public function __construct(
        private EquivalentRepositoryInterface $equivalentRepository,
    )
    {
    }

    public function execute(EquivalentCreateData $dto, UserPermission $userPermission): EquivalentEntity
    {
        if (!$userPermission->can('catalog.product.create')) {
            throw new AccessDeniedException();
        }

        $equivalent = new EquivalentEntity(
            name: trim($dto->name),
            categoryId: $dto->categoryId,
        );

        return $this->equivalentRepository->save($equivalent);
    }
}
