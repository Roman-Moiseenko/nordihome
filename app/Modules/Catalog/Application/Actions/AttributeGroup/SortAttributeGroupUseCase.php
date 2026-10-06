<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\AttributeGroup;

use App\Modules\Catalog\Application\DTOs\AttributeGroup\AttributeGroupSortData;
use App\Modules\Catalog\Domain\Interfaces\AttributeGroupRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class SortAttributeGroupUseCase
{
    public function __construct(
        private AttributeGroupRepositoryInterface $attributeGroupRepository,
    )
    {
    }

    /**
     * Сортирует группу. Группа с указанным id получает новый sort,
     * остальные группы пересчитываются.
     */
    public function execute(AttributeGroupSortData $dto, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        $this->attributeGroupRepository->updateSortOrder($dto->id, $dto->sort);
    }
}
