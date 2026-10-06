<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\AttributeGroup;

use App\Modules\Catalog\Application\DTOs\AttributeGroup\AttributeGroupUpdateData;
use App\Modules\Catalog\Domain\Entities\AttributeGroupEntity;
use App\Modules\Catalog\Domain\Interfaces\AttributeGroupRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateAttributeGroupUseCase
{
    public function __construct(
        private AttributeGroupRepositoryInterface $attributeGroupRepository,
    )
    {
    }

    public function execute(int $groupId, AttributeGroupUpdateData $dto, UserPermission $userPermission): AttributeGroupEntity
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        $group = $this->attributeGroupRepository->getById($groupId);

        $group->name = $dto->name;

        if ($dto->svg !== null) {
            $group->svg = trim($dto->svg) === '' ? null : $dto->svg;
        }

        return $this->attributeGroupRepository->save($group);
    }
}
