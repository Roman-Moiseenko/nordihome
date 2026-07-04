<?php

namespace App\Modules\Parser\Application\Actions\Category;

use App\Modules\Catalog\Application\DTOs\Category\CategoryCreateData;
use App\Modules\Catalog\Application\Interfaces\CategoryRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\CategoryEntity;
use App\Modules\Parser\Application\DTOs\Category\ParserCategoryCreateData;
use App\Modules\Parser\Application\Interfaces\ParserCategoryRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserCategoryEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class CreateParserCategoryUseCase
{
    public function __construct(
        private ParserCategoryRepositoryInterface $categoryRepository,
    )
    {
    }

    public function execute(ParserCategoryCreateData $dto, UserPermission $userPermission): ParserCategoryEntity
    {
        // Проверка прав доступа
        if (!$userPermission->can('parser.category.create')) throw new \DomainException('Доступ запрещён');


        $slug = new Slug($dto->name);

        // Если slug занят, добавляем суффикс
        if ($this->categoryRepository->existsSlug((string)$slug)) {
            $slug = new Slug((string)$slug . '-' . uniqid());
        }

        $category = new ParserCategoryEntity(
            name: $dto->name,
            slug: $slug,
            ikeaId: $dto->ikeaId,
            parentId: $dto->parentId ?: null,
        );

        return $this->categoryRepository->save($category);
    }
}
