<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\Actions\Promotion;

use App\Modules\Discount\Application\DTOs\Promotion\PromotionCreateData;
use App\Modules\Discount\Application\Interfaces\PromotionRepositoryInterface;
use App\Modules\Discount\Domain\Entities\PromotionEntity;
use App\Modules\Discount\Domain\ValueObjects\PromotionStatus;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class CreatePromotionUseCase
{
    public function __construct(
        private PromotionRepositoryInterface $promotionRepository,
    )
    {
    }

    /**
     * Создать акцию (принимается только название).
     */
    public function execute(PromotionCreateData $dto, UserPermission $permission): PromotionEntity
    {
        if (!$permission->can('discount.promotion.create')) throw new AccessDeniedException();


        $promotion = new PromotionEntity(
            name: $dto->name,
            slug: new Slug($dto->name),
        );
        $promotion->status = PromotionStatus::default();
        return $this->promotionRepository->save($promotion);
    }
}
