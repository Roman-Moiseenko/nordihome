<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\Actions\Category;

use App\Modules\Parser\Application\Interfaces\ParserCategoryRepositoryInterface;
use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class ToggleProductsAvailabilityUseCase
{
    public function __construct(
        private ParserCategoryRepositoryInterface $categoryRepository,
        private ParserProductRepositoryInterface $productRepository,
    ) {}

    /**
     * Переключает availability у всех товаров, привязанных к категории и её дочерним.
     * Возвращает количество затронутых товаров.
     */
    public function execute(int $categoryId, bool $active, UserPermission $userPermission): int
    {
        if (!$userPermission->can('parser.product.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        // 1. Получаем ID дочерних категорий (включая текущую)
        $descendantIds = $this->categoryRepository->getDescendantIds($categoryId);
        $allCategoryIds = array_merge([$categoryId], $descendantIds);

        // 2. Получаем товары из этих категорий
        $products = $this->productRepository->getByCategoryIds($allCategoryIds);

        if (empty($products)) {
            return 0;
        }

        // 3. Переключаем availability
        $productIds = array_map(fn($p) => $p->id, $products);
        $this->productRepository->bulkToggleAvailability($productIds, $active);

        return count($productIds);
    }
}
