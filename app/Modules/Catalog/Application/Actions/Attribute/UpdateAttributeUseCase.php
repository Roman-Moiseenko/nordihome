<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Attribute;

use App\Modules\Catalog\Application\DTOs\Attribute\AttributeUpdateData;
use App\Modules\Catalog\Domain\Entities\AttributeEntity;
use App\Modules\Catalog\Domain\Entities\AttributeVariantEntity;
use App\Modules\Catalog\Domain\Interfaces\AttributeCategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\AttributeRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\AttributeType;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateAttributeUseCase
{
    public function __construct(
        private AttributeRepositoryInterface $attributeRepository,
        private AttributeCategoryRepositoryInterface $attributeCategoryRepository,
    )
    {
    }

    public function execute(int $id, AttributeUpdateData $dto, UserPermission $userPermission): AttributeEntity
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        $attribute = $this->attributeRepository->getById($id);

        $attribute->name = trim($dto->name);
        $attribute->type = new AttributeType($dto->type);
        $attribute->groupId = $dto->groupId;
        $attribute->multiple = $dto->multiple;
        $attribute->filter = $dto->filter;
        $attribute->showIn = $dto->showIn;
        $attribute->sameAs = $dto->sameAs !== null ? trim($dto->sameAs) : null;

        // Если тип изменился с "variant" на другой — очищаем варианты,
        // репозиторий при сохранении удалит их (вместе с фото).
        if ($attribute->type->isVariant()) {
            $attribute->variants = $this->buildVariants($dto->variants);
        } else {
            $attribute->variants = [];
        }

        $attribute = $this->attributeRepository->save($attribute);

        $this->attributeCategoryRepository->syncCategories($attribute->id, $dto->categories);

        return $attribute;
    }

    /**
     * @param array<int, array{id?: ?int, name?: ?string}>|null $items
     * @return AttributeVariantEntity[]
     */
    private function buildVariants(?array $items): array
    {
        $variants = [];

        foreach ($items ?? [] as $item) {
            $name = isset($item['name']) && !is_null($item['name'])
                ? trim((string) $item['name'])
                : '';

            if ($name === '') {
                continue;
            }

            $variant = new AttributeVariantEntity($name);

            if (!empty($item['id'])) {
                $variant->id = (int) $item['id'];
            }

            $variants[] = $variant;
        }

        return $variants;
    }
}
