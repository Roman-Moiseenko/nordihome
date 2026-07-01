<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Category;

use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class RemoveCategoryUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): void
    {
        // Проверка прав доступа
        if (!$userPermission->can('catalog.category.delete')) {
            throw new \DomainException('Доступ запрещён');
        }

        // Проверка на наличие дочерних категорий
        if ($this->categoryRepository->hasChildren($id)) {
            throw new \DomainException('Нельзя удалить категорию с подкатегориями');
        }

        $this->categoryRepository->delete($id);
    }
}
