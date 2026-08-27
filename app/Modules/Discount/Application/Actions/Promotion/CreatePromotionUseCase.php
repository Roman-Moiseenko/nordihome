<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\Actions\Promotion;

use App\Modules\Discount\Application\DTOs\Promotion\PromotionCreateData;
use App\Modules\Discount\Application\Interfaces\PromotionRepositoryInterface;
use App\Modules\Discount\Domain\Entities\PromotionEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;
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
    public function execute(PromotionCreateData $dto, UserPermission $userPermission): PromotionEntity
    {
        if (!$userPermission->can('discount.promotion.create')) {
            throw new \DomainException('Доступ запрещён');
        }

        $promotion = new PromotionEntity(
            name: $dto->name,
            slug: new Slug($dto->name),
        );

        return $this->promotionRepository->save($promotion);
    }
}
