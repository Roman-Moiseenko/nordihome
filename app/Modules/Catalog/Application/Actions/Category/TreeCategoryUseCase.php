<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Category;

use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;

readonly class TreeCategoryUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    )
    {
    }

    /**
     * @return CategoryEntity[]
     */
    public function execute(): array
    {
        return $this->categoryRepository->getTree();
    }
}
