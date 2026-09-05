<?php

namespace App\Modules\Discount\Application\Actions\Promotion;

use App\Modules\Discount\Domain\Interfaces\PromotionRepositoryInterface;
use App\Modules\Discount\Domain\ValueObjects\PromotionStatus;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class StatusWaitingPromotionUseCase
{

    public function __construct(private PromotionRepositoryInterface $promotionRepository)
    {

    }
    public function execute(int $promotionId, UserPermission$permission):void
    {
        if (!$permission->can('discount.promotion.edit')) throw new AccessDeniedException();

        $promotion = $this->promotionRepository->getById($promotionId);
        //TODO Проверить кол-во товаров
        if (!$promotion->status->isDraft()) throw new \DomainException('Нельзя опубликовать акцию');
        $promotion->status = PromotionStatus::waiting();

        $this->promotionRepository->save($promotion);

    }
}
