<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\Actions\CategoryProduct;

use App\Modules\Parser\Application\Interfaces\CategoryProductParserRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class AttachCategoriesToProductUseCase
{
    public function __construct(
        private CategoryProductParserRepositoryInterface $categoryProductRepository,
    )
    {
    }

    /**
     * Добавить категории к товару (attach — дополняет существующие).
     *
     * @param int   $productId
     * @param int[] $categoryIds
     * @param UserPermission $userPermission
     */
    public function execute(int $productId, array $categoryIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('parser.product.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $this->categoryProductRepository->attachCategories($productId, $categoryIds);
    }
}
