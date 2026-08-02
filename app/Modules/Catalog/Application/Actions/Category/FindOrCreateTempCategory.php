<?php

namespace App\Modules\Catalog\Application\Actions\Category;

use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
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
        $category = $this->categoryRepository->getBySlug(CategoryEntity::TEMP_IKEA);
        if (is_null($category)) {
            $category = new CategoryEntity(
                name: '!!! ТОВАРЫ ИКЕА ПАРСЕР !!!',
                slug: new Slug(CategoryEntity::TEMP_IKEA),
                parentId: null,
            );
            $category->published = false;
            $category = $this->categoryRepository->save($category);
        }
        return $category;
    }
}
