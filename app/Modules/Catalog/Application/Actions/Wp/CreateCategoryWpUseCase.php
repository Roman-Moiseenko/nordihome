<?php

namespace App\Modules\Catalog\Application\Actions\Wp;

use App\Modules\Catalog\Application\DTOs\Category\CategoryCreateData;
use App\Modules\Catalog\Application\DTOs\Wp\CategoryRoomWpData;
use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\Slug;

class CreateCategoryWpUseCase
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
    )
    {
    }

    public function execute(CategoryRoomWpData $dto, UserPermission $userPermission):? CategoryEntity
    {
        // Проверка прав доступа
        if (!$userPermission->can('catalog.category.create')) throw new \DomainException('Доступ запрещён');

        if ($this->categoryRepository->existsByWpId($dto->wpId)) return null;

        $slug = new Slug($dto->name);

        // Если slug занят, добавляем суффикс
        if ($this->categoryRepository->existsSlug((string)$slug)) {
            $slug = new Slug((string)$slug . '-' . uniqid());
        }


        $category = new CategoryEntity(
            name: $dto->name,
            slug: $slug,
            parentId: $dto->parentId ?: null,
        );
        $category->wpId = $dto->wpId;

        return $this->categoryRepository->save($category);
    }
}
