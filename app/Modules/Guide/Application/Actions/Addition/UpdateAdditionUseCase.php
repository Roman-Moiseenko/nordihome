<?php

namespace App\Modules\Guide\Application\Actions\Addition;

use App\Modules\Guide\Application\DTOs\Addition\AdditionUpdateData;
use App\Modules\Guide\Domain\Entities\AdditionEntity;
use App\Modules\Guide\Domain\Interfaces\AdditionRepositoryInterface;
use App\Modules\Guide\Domain\ValueObjects\AdditionType;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class UpdateAdditionUseCase
{
    public function __construct(
        private AdditionRepositoryInterface $repository
    )
    {

    }
    public function execute(int $additionId, AdditionUpdateData $dto, UserPermission $permission): AdditionEntity
    {
        if (!$permission->can('guide.guide.edit')) throw new AccessDeniedException();

        $entity = $this->repository->getById($additionId);

        if (!is_null($dto->name)) $entity->name = $dto->name;
        if (!is_null($dto->base)) $entity->base = $dto->base;
        if (!is_null($dto->slug)) $entity->slug = new Slug($dto->slug);
        if (!is_null($dto->type)) $entity->type = new AdditionType($dto->type);
        if (!is_null($dto->manual)) $entity->manual = $dto->manual;
        if (!is_null($dto->isQuantity)) $entity->isQuantity = $dto->isQuantity;
        $entity->class = $dto->class;


        return $this->repository->save($entity);
    }
}
