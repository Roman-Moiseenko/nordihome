<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Attribute;

use App\Modules\Catalog\Application\DTOs\Attribute\AttributeCreateData;
use App\Modules\Catalog\Domain\Entities\AttributeEntity;
use App\Modules\Catalog\Domain\Interfaces\AttributeCategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\AttributeRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\AttributeType;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CreateAttributeUseCase
{
    public function __construct(
        private AttributeRepositoryInterface $attributeRepository,
        private AttributeCategoryRepositoryInterface $attributeCategoryRepository,
    )
    {
    }

    public function execute(AttributeCreateData $dto, UserPermission $userPermission): AttributeEntity
    {
        if (!$userPermission->can('catalog.product.create')) {
            throw new AccessDeniedException();
        }

        $attribute = new AttributeEntity(
            name: $dto->name,
            type: new AttributeType($dto->type),
            groupId: $dto->groupId,
        );

        $attribute = $this->attributeRepository->save($attribute);

        if (!empty($dto->categories)) {
            $this->attributeCategoryRepository->syncCategories($attribute->id, $dto->categories);
        }

        return $attribute;
    }
}
