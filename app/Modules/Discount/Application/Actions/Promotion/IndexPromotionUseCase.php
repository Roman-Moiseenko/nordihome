<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\Actions\Promotion;

use App\Modules\Discount\Application\DTOs\Promotion\PromotionIndexData;
use App\Modules\Discount\Application\Interfaces\PromotionProductRepositoryInterface;
use App\Modules\Discount\Application\Interfaces\PromotionRepositoryInterface;
use App\Modules\Discount\Domain\Entities\PromotionEntity;
use App\Modules\Shared\Application\Actions\GetImageThumbUseCase;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class IndexPromotionUseCase
{
    public function __construct(
        private PromotionRepositoryInterface $promotionRepository,
        private PromotionProductRepositoryInterface $promotionProductRepository,
        private GetImageThumbUseCase $getImageThumbUseCase,
    )
    {
    }

    /**
     * @return PromotionIndexData[]
     */
    public function execute(UserPermission $userPermission): array
    {
        if (!$userPermission->can('discount.promotion.view')) {
            throw new AccessDeniedException();
        }


        $promotions = $this->promotionRepository->getAll();

        \Log::info(json_encode($promotions));

        return array_map(
            fn(PromotionEntity $promotion) => PromotionIndexData::fromEntity(
                $promotion,
                $this->promotionProductRepository->countProductsByPromotionId($promotion->id ?? 0),
                $this->getImageThumbUseCase->execute($promotion->id, 'discount.promotion', 'mini'),
            ),
            $promotions,
        );
    }
}
