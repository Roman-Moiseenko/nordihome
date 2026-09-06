<?php

namespace App\Modules\Guide\Application\Actions\Addition;

use App\Modules\Guide\Application\DTOs\Addition\AdditionCreateData;
use App\Modules\Guide\Domain\Entities\AdditionEntity;
use App\Modules\Guide\Domain\Interfaces\AdditionRepositoryInterface;
use App\Modules\Guide\Domain\ValueObjects\AdditionType;

use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class CreateAdditionUseCase
{
    public function __construct(
        private AdditionRepositoryInterface $repository
    )
    {

    }

    public function execute(AdditionCreateData $dto, UserPermission $permission): AdditionEntity
    {
        if (!$permission->can('guide.guide.create')) throw new AccessDeniedException();

        if (!is_null($dto->slug)) {
            $entity = $this->repository->findBySlug($dto->slug);
            if (!is_null($entity)) return $entity;
        }

        $entity = new AdditionEntity(
            name: $dto->name,
            slug: new Slug($dto->slug ?? $dto->name),
            type: new AdditionType($dto->type),
        );

        $entity->base = $dto->base;
        $entity->class = $dto->class;
        $entity->manual = $dto->manual;
        $entity->isQuantity = $dto->isQuantity;
        return $this->repository->save($entity);
    }
}
