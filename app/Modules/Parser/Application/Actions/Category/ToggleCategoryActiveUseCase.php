<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\Actions\Category;

use App\Modules\Parser\Application\Interfaces\ParserCategoryRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserCategoryEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class ToggleCategoryActiveUseCase
{
    public function __construct(
        private ParserCategoryRepositoryInterface $categoryRepository,
    ) {}

    /**
     * Переключает active для категории и всех дочерних.
     * Возвращает новое состояние active.
     */
    public function execute(int $categoryId, UserPermission $userPermission): bool
    {
        if (!$userPermission->can('parser.category.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $category = $this->categoryRepository->getById($categoryId);
        $newActive = !$category->isActive();

        // Используем уже готовый метод из репозитория (Nested Set)
        $this->categoryRepository->toggleActive($categoryId);

        return $newActive;
    }
}
