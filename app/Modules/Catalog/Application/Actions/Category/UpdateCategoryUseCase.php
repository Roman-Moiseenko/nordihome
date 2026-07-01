<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Category;

use App\Modules\Catalog\Application\DTOs\Category\CategoryUpdateData;
use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;
use Illuminate\Support\Str;

readonly class UpdateCategoryUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    )
    {
    }

    public function execute(int $id, CategoryUpdateData $dto, UserPermission $userPermission): CategoryEntity
    {
        // Проверка прав доступа
        if (!$userPermission->can('catalog.category.edit')) throw new \DomainException('Доступ запрещён');

        $category = $this->categoryRepository->getById($id);

        // Обновляем поля, если переданы
        if ($dto->name !== null) {
            $category->name = $dto->name;
        }

        // Обновляем slug
        $slugValue = $dto->slug;
        if ($slugValue !== null || $dto->name !== null) {
            $slugString = $slugValue !== null ? trim($slugValue) : '';
            if ($slugString === '') {
                $slugString = Str::slug($category->name);
            }
            $slug = new Slug($slugString);
            if ($this->categoryRepository->existsSlug((string)$slug, $id)) {
                $slug = new Slug((string)$slug . '-' . uniqid());
            }
            $category->slug = $slug;
        }

        if ($dto->parentId !== null) {
            $category->parentId = $dto->parentId;
        }

        if ($dto->svgIcon !== null) {
            $category->svgIcon = $dto->svgIcon;
        }

        if ($dto->published !== null) {
            $dto->published ? $category->publish() : $category->unpublish();
        }

        // Обновляем Meta
        if ($dto->metaTitle !== null || $dto->metaDescription !== null) {
            $currentMeta = $category->meta ?? Meta::default();
            $category->meta = new Meta(
                title: $dto->metaTitle ?? $currentMeta->getTitle(),
                description: $dto->metaDescription ?? $currentMeta->getDescription(),
            );
        }

        return $this->categoryRepository->save($category);
    }
}
