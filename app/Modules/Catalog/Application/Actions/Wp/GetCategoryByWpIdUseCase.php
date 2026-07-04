<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Wp;

use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;

readonly class GetCategoryByWpIdUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    )
    {
    }

    /**
     * Получить категорию по wp_id.
     * Возвращает null, если категория не найдена или содержит дочерние элементы.
     */
    public function execute(int $wpId): ?CategoryEntity
    {
        $category = $this->categoryRepository->findByWpId($wpId);

        if ($category === null) return null;

        if ($this->categoryRepository->hasChildren($category->id)) return null;

        return $category;
    }
}
