<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoCommonImage;

use App\Modules\Marketplace\Application\DTOs\AvitoCommonImage\AvitoCommonImageCreateData;
use App\Modules\Marketplace\Domain\Entities\AvitoCommonImageEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCommonImageRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CreateAvitoCommonImageUseCase
{
    public function __construct(
        private AvitoCommonImageRepositoryInterface $repository,
    ) {}

    public function execute(AvitoCommonImageCreateData $dto, UserPermission $permission): AvitoCommonImageEntity
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $entity = new AvitoCommonImageEntity(name: $dto->name);
        $entity->sort = $this->repository->nextSort();

        return $this->repository->save($entity);
    }
}
