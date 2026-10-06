<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\AttributeGroup;

use App\Modules\Catalog\Domain\Interfaces\AttributeGroupRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemoveAttributeGroupUseCase
{
    public function __construct(
        private AttributeGroupRepositoryInterface $attributeGroupRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        if ($this->attributeGroupRepository->hasAttributes($id)) {
            throw new \DomainException('Нельзя удалить группу с атрибутами');
        }

        $this->attributeGroupRepository->delete($id);
    }
}
