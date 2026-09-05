<?php

namespace App\Modules\Discount\Application\Actions\Promotion;

use App\Modules\Discount\Domain\Interfaces\PromotionRepositoryInterface;
use App\Modules\Discount\Domain\ValueObjects\PromotionStatus;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class StatusDraftPromotionUseCase
{

    public function __construct(private PromotionRepositoryInterface $promotionRepository)
    {

    }
    public function execute(int $promotionId, UserPermission$permission):void
    {
        if (!$permission->can('discount.promotion.edit')) throw new AccessDeniedException();
        $promotion = $this->promotionRepository->getById($promotionId);
        $promotion->status = PromotionStatus::draft();
        $promotion->startAt = null;
        $promotion->finishAt = null;
        $this->promotionRepository->save($promotion);

    }
}
