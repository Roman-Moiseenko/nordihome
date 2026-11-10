<?php

namespace App\Modules\Catalog\Application\Actions\Category;

use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Catalog\Domain\Interfaces\CategoryRepositoryInterface;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class FindOrCreateTempCategory
{

    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    )
    {
    }

    public function execute(): CategoryEntity
    {
        $category = $this->categoryRepository->getBySlug(CategoryEntity::TEMP_CATEGORY);
        if (is_null($category)) {
            $category = new CategoryEntity(
                name: '!!! ТОВАРЫ НЕРАСПРЕДЕЛЕННЫЕ !!!',
                slug: new Slug(CategoryEntity::TEMP_CATEGORY),
                parentId: null,
            );
            $category->published = false;
            $category = $this->categoryRepository->save($category);
        }
        return $category;
    }
}
