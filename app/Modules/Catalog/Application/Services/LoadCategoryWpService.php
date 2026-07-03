<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Services;

use App\Modules\Catalog\Application\Actions\Category\ToggleCategoryUseCase;
use App\Modules\Catalog\Application\Actions\Wp\CreateCategoryWpUseCase;
use App\Modules\Catalog\Application\DTOs\Wp\CategoryRoomWpData;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Shared\Application\Actions\UploadPhotoByUrlUseCase;
use App\Modules\Shared\Application\DTOs\Photo\PhotoUploadByUrlData;
use App\Modules\Shared\Domain\Entities\UserPermission;


class LoadCategoryWpService
{
    private UserPermission $userPermission;
    private int $count = 0;
    public function __construct(
        private readonly CreateCategoryWpUseCase     $categoryWpUseCase,
        private readonly UploadPhotoByUrlUseCase $uploadPhotoByUrlUseCase,
        private readonly ToggleCategoryUseCase $toggleCategoryUseCase,
    )
    {
    }

    /**
     * Загрузить категории из WP массива (children корневого каталога)
     *
     * @param array $categories Массив категорий $categories[self::CATALOG_ID]['children']
     * @return int Количество созданных категорий
     */
    public function load(array $categories): int
    {
        $this->userPermission = new UserPermission(
            null,
            ['admin'],
            ['storage.photo.upload', 'catalog.category.create', 'catalog.category.edit']
        );

        $this->loadChildren($categories['children'] ?? [], null);

        return $this->count;
    }

    private function createCategory(array $categoryData, ?int $parentId):? CategoryEntity
    {
        $dto = CategoryRoomWpData::fromWpArray($categoryData, $parentId);

        $category = $this->categoryWpUseCase->execute($dto, $this->userPermission);

        if (!is_null($category)) {
            if (!$category->isPublished())
                $this->toggleCategoryUseCase->execute($category->id, $this->userPermission);

            if ($categoryData['img'] != false) {
                $dtoPhoto = new PhotoUploadByUrlData(
                    $category->id,
                    'catalog.category',
                    'image',
                    $categoryData['img']
                );
                $this->uploadPhotoByUrlUseCase->execute($dtoPhoto, $this->userPermission);
            }
            $this->count++;
        }
        return $category;
    }

    /**
     * Рекурсивная загрузка дочерних категорий
     */
    private function loadChildren(array $children, ?int $parentId): void
    {
        if (empty($children)) return;

        foreach ($children as $childData) {
            $category = $this->createCategory($childData, $parentId);
            if ($category !== null)$this->loadChildren($childData['children'] ?? [], $category->id);
        }
    }




}
