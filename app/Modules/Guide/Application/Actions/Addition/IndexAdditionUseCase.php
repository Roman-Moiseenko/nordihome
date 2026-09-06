<?php

namespace App\Modules\Guide\Application\Actions\Addition;

use App\Modules\Guide\Application\DTOs\Addition\AdditionCreateData;
use App\Modules\Guide\Application\DTOs\Addition\AdditionIndexData;
use App\Modules\Guide\Domain\Entities\AdditionEntity;
use App\Modules\Guide\Domain\Interfaces\AdditionRepositoryInterface;
use App\Modules\Guide\Domain\ValueObjects\AdditionType;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class IndexAdditionUseCase
{
    public function __construct(
        private AdditionRepositoryInterface $repository
    )
    {

    }

    public function execute(UserPermission $permission): array
    {
        if (!$permission->can('guide.guide.view')) throw new AccessDeniedException();

        $entities = $this->repository->getAll();

        return array_map(function (AdditionEntity $entity) {
            $typeName = AdditionType::TYPES[$entity->type->value];
            $className = is_null($entity->class) ? '' : $entity->class::getName();

            return AdditionIndexData::fromEntity($entity, $typeName, $className);
        }, $entities);

    }
}
