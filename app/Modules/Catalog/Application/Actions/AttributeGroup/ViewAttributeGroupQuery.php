<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\AttributeGroup;

use App\Modules\Catalog\Domain\Entities\AttributeGroupEntity;
use App\Modules\Catalog\Domain\Interfaces\AttributeGroupRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ViewAttributeGroupQuery
{
    public function __construct(
        private AttributeGroupRepositoryInterface $attributeGroupRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): AttributeGroupEntity
    {
        if (!$userPermission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        return $this->attributeGroupRepository->getById($id);
    }
}
