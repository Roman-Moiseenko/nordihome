<?php

namespace App\Modules\Catalog\Application\Actions\Category;

use App\Modules\Catalog\Application\DTOs\Category\CategoryCreateData;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Catalog\Domain\Interfaces\CategoryRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\Slug;

class CreateCategoryUseCase
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
    )
    {
    }

    public function execute(CategoryCreateData $dto, UserPermission $userPermission): CategoryEntity
    {
        // Проверка прав доступа
        if (!$userPermission->can('catalog.category.create')) throw new \DomainException('Доступ запрещён');


        $slug = new Slug($dto->slug ?: $dto->name);

        // Если slug занят, добавляем суффикс
        if ($this->categoryRepository->existsSlug((string)$slug)) {
            $slug = new Slug((string)$slug . '-' . uniqid());
        }

        $category = new CategoryEntity(
            name: $dto->name,
            slug: $slug,
            parentId: $dto->parentId ?: null,
        );

        return $this->categoryRepository->save($category);
    }
}
