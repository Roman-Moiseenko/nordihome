<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\AttributeGroup;

use App\Modules\Catalog\Application\DTOs\AttributeGroup\AttributeGroupIndexData;
use App\Modules\Catalog\Domain\Entities\AttributeGroupEntity;
use App\Modules\Catalog\Domain\Interfaces\AttributeGroupRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class IndexAttributeGroupQuery
{
    public function __construct(
        private AttributeGroupRepositoryInterface $attributeGroupRepository,
    )
    {
    }

    /**
     * @return AttributeGroupIndexData[]
     */
    public function execute(UserPermission $userPermission): array
    {
        if (!$userPermission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        $groups = $this->attributeGroupRepository->getAll();

        $ids = array_map(
            fn(AttributeGroupEntity $group) => $group->id,
            $groups,
        );

        $counts = $this->attributeGroupRepository->countAttributesByGroupIds($ids);

        return array_map(
            fn(AttributeGroupEntity $group) => AttributeGroupIndexData::fromEntity(
                $group,
                $counts[$group->id] ?? 0,
            ),
            $groups,
        );
    }
}
