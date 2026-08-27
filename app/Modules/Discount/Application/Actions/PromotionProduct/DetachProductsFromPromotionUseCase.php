<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\Actions\PromotionProduct;

use App\Modules\Discount\Application\Interfaces\PromotionProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class DetachProductsFromPromotionUseCase
{
    public function __construct(
        private PromotionProductRepositoryInterface $promotionProductRepository,
    )
    {
    }

    /**
     * Отвязать товары от акции.
     *
     * @param int $promotionId
     * @param int[] $productIds
     * @param UserPermission $userPermission
     */
    public function execute(int $promotionId, array $productIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('discount.promotion.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $this->promotionProductRepository->detachProducts($promotionId, $productIds);
    }
}
