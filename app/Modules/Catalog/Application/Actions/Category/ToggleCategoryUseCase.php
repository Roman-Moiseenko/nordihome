<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Category;

use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class ToggleCategoryUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): void
    {
        // Проверка прав доступа
        if (!$userPermission->can('catalog.category.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $category = $this->categoryRepository->getById($id);

        // Новое значение published (инвертируем текущее)
        $newPublished = !$category->isPublished();

        // Меняем published у самой категории
        $category->published = $newPublished;
        $this->categoryRepository->save($category);

        // Меняем published у всех дочерних категорий
        $descendantIds = $this->categoryRepository->getDescendantIds($id);
        if (!empty($descendantIds)) {
            $this->categoryRepository->bulkTogglePublished($descendantIds, $newPublished);
        }
    }
}
