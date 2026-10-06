<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\AttributeGroup;

use App\Modules\Catalog\Application\DTOs\AttributeGroup\AttributeGroupCreateData;
use App\Modules\Catalog\Domain\Entities\AttributeGroupEntity;
use App\Modules\Catalog\Domain\Interfaces\AttributeGroupRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CreateAttributeGroupUseCase
{
    public function __construct(
        private AttributeGroupRepositoryInterface $attributeGroupRepository,
    )
    {
    }

    public function execute(AttributeGroupCreateData $dto, UserPermission $userPermission): AttributeGroupEntity
    {
        if (!$userPermission->can('catalog.product.create')) {
            throw new AccessDeniedException();
        }

        $group = new AttributeGroupEntity(
            name: $dto->name,
        );

        return $this->attributeGroupRepository->save($group);
    }
}
