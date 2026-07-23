<?php

namespace App\Modules\Catalog\Application\Actions\Tag;

use App\Modules\Catalog\Application\DTOs\Tag\TagCreateData;
use App\Modules\Catalog\Application\Interfaces\TagProductRepositoryInterface;
use App\Modules\Catalog\Application\Interfaces\TagRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\TagEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class RemoveTagUseCase
{
    public function __construct(
        private TagRepositoryInterface        $tagRepository,
        private TagProductRepositoryInterface $tagProductRepository,
    )
    {}

    public function execute(int $tagId, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.delete')) throw new \DomainException('Доступ запрещён');

        // Проверка на наличие дочерних категорий
        if ($this->tagProductRepository->countProductsByTagId($tagId)> 0) {
            throw new \DomainException('Нельзя удалить метку с товарами');
        }

        $this->tagRepository->delete($tagId);

    }
}
