<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Category;

use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class IndexCategoryUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    )
    {
    }

    /**
     * @return CategoryEntity[]
     */
    public function execute(UserPermission $userPermission): array
    {
        // Проверка прав доступа
        if (!$userPermission->can('catalog.category.view')) throw new \DomainException('Доступ запрещён');

        return $this->categoryRepository->getAll();
    }
}
