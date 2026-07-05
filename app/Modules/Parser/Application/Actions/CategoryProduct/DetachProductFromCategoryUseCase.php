<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\Actions\CategoryProduct;

use App\Modules\Parser\Application\Interfaces\CategoryProductParserRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class DetachProductFromCategoryUseCase
{
    public function __construct(
        private CategoryProductParserRepositoryInterface $categoryProductRepository,
    )
    {
    }

    /**
     * Отвязать товары от категории.
     *
     * @param int   $categoryId
     * @param int[] $productIds
     * @param UserPermission $userPermission
     */
    public function execute(int $categoryId, array $productIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('parser.category.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $this->categoryProductRepository->detachProducts($categoryId, $productIds);
    }
}
