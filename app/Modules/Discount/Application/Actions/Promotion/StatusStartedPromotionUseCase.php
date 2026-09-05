<?php

namespace App\Modules\Discount\Application\Actions\Promotion;

use App\Modules\Discount\Domain\Interfaces\PromotionRepositoryInterface;
use App\Modules\Discount\Domain\ValueObjects\PromotionStatus;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class StatusStartedPromotionUseCase
{

    public function __construct(private PromotionRepositoryInterface $promotionRepository)
    {

    }
    public function execute(int $promotionId, UserPermission $permission):void
    {
        if (!$permission->can('discount.promotion.edit')) throw new AccessDeniedException();

        $promotion = $this->promotionRepository->getById($promotionId);
        if ($promotion->status->isFinished()) {
            $promotion->finishAt = null;
        }

        $promotion->status = PromotionStatus::started();
        $promotion->startAt = new \DateTimeImmutable();
        $this->promotionRepository->save($promotion);

    }
}
