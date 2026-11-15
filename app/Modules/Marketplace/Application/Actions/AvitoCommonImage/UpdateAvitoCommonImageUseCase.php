<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoCommonImage;

use App\Modules\Marketplace\Application\DTOs\AvitoCommonImage\AvitoCommonImageUpdateData;
use App\Modules\Marketplace\Domain\Entities\AvitoCommonImageEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCommonImageRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateAvitoCommonImageUseCase
{
    public function __construct(
        private AvitoCommonImageRepositoryInterface $repository,
    ) {}

    public function execute(AvitoCommonImageUpdateData $dto, UserPermission $permission): AvitoCommonImageEntity
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $entity = $this->repository->getById($dto->id);
        $entity->name = $dto->name;

        if ($dto->active !== null) {
            $dto->active ? $entity->activate() : $entity->deactivate();
        }

        return $this->repository->save($entity);
    }
}
