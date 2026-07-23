<?php

namespace App\Modules\Catalog\Application\Actions\TagProduct;

use App\Modules\Catalog\Application\Interfaces\TagProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

class AttachProductsToTagUseCase
{
    public function __construct(
        private TagProductRepositoryInterface $tagProductRepository,
    )
    {
    }

    public function execute(int $tagId, array $productIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $this->tagProductRepository->attachProducts($tagId, $productIds);
    }
}
