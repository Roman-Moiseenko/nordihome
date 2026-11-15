<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoParameterRegistry;

use App\Modules\Marketplace\Application\DTOs\AvitoParameterRegistry\AvitoParameterRegistryUpdateData;
use App\Modules\Marketplace\Domain\Entities\AvitoParameterRegistryEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoParameterRegistryRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateAvitoParameterRegistryUseCase
{
    public function __construct(
        private AvitoParameterRegistryRepositoryInterface $registryRepository,
    ) {}

    public function execute(
        AvitoParameterRegistryUpdateData $dto,
        UserPermission $permission
    ): AvitoParameterRegistryEntity {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $entity = $this->registryRepository->getById($dto->id);
        $entity->kind = $dto->kind;
        // Имя поля сущности храним только для kind = product.
        $entity->field = $dto->kind === AvitoParameterRegistryEntity::KIND_PRODUCT
            ? $dto->field
            : null;

        return $this->registryRepository->save($entity);
    }
}
