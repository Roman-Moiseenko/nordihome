<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\AttributeGroup;

use App\Modules\Catalog\Application\DTOs\AttributeGroup\AttributeGroupAttributeData;
use App\Modules\Catalog\Domain\Interfaces\AttributeGroupRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class ListAttributeGroupAttributesQuery
{
    public function __construct(
        private AttributeGroupRepositoryInterface $attributeGroupRepository,
    )
    {
    }

    /**
     * Список атрибутов группы без пагинации (атрибуты получаются через отношение).
     *
     * @return AttributeGroupAttributeData[]
     */
    public function execute(int $id, UserPermission $userPermission): array
    {
        if (!$userPermission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        return $this->attributeGroupRepository->getAttributes($id);
    }
}
