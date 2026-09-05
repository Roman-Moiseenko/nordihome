<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\Actions\PromotionProduct;

use App\Modules\Discount\Application\DTOs\PromotionProduct\PromotionProductPriceData;
use App\Modules\Discount\Domain\Interfaces\PromotionProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class SetProductPriceUseCase
{
    public function __construct(
        private PromotionProductRepositoryInterface $promotionProductRepository,
    )
    {
    }

    /**
     * Установить цену товара в акции.
     *
     * @param int $promotionId
     * @param PromotionProductPriceData $dto
     * @param UserPermission $userPermission
     */
    public function execute(int $promotionId, PromotionProductPriceData $dto, UserPermission $userPermission): void
    {
        if (!$userPermission->can('discount.promotion.edit')) throw new AccessDeniedException();

        $this->promotionProductRepository->setPrice($promotionId, $dto->productId, $dto->price);
    }
}
