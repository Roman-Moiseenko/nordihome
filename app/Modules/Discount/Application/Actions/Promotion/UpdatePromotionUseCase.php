<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\Actions\Promotion;

use App\Modules\Discount\Application\DTOs\Promotion\PromotionUpdateData;
use App\Modules\Discount\Domain\Entities\PromotionEntity;
use App\Modules\Discount\Domain\Interfaces\PromotionRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;
use App\Modules\Shared\Domain\ValueObjects\Meta;
use App\Modules\Shared\Domain\ValueObjects\Slug;

readonly class UpdatePromotionUseCase
{
    public function __construct(
        private PromotionRepositoryInterface $promotionRepository,
    )
    {
    }

    /**
     * Обновить данные акции. Обновляются только переданные (не null) поля.
     */
    public function execute(int $id, PromotionUpdateData $dto, UserPermission $permission): PromotionEntity
    {
        if (!$permission->can('discount.promotion.edit')) throw new AccessDeniedException();

        $promotion = $this->promotionRepository->getById($id);

        if ($dto->name !== null) $promotion->name = $dto->name;

        if ($dto->slug !== null) $promotion->slug = new Slug($dto->slug);

        // Обновляем Meta
        if ($dto->metaTitle !== null || $dto->metaDescription !== null) {
            $currentMeta = $promotion->meta ?? Meta::default();
            $promotion->meta = new Meta(
                title: $dto->metaTitle ?? $currentMeta->getTitle(),
                description: $dto->metaDescription ?? $currentMeta->getDescription(),
            );
        }

        if ($dto->conditionUrl !== null) $promotion->conditionUrl = $dto->conditionUrl;

        if ($dto->menu !== null) $promotion->menu = $dto->menu;

        if ($dto->showTitle !== null) $promotion->showTitle = $dto->showTitle;

        if ($dto->discount !== null) $promotion->discount = $dto->discount;

        $promotion->startAt = is_null($dto->startAt) ? null : new \DateTimeImmutable($dto->startAt);

        $promotion->finishAt = is_null($dto->finishAt) ? null : new \DateTimeImmutable($dto->finishAt);

        if ($dto->colorClass !== null) $promotion->colorClass = $dto->colorClass;

        if ($dto->positionClass !== null) $promotion->positionClass = $dto->positionClass;

        if ($dto->textTag !== null) $promotion->textTag = $dto->textTag;

        if ($dto->showTag !== null) $promotion->showTag = $dto->showTag;

        if ($dto->showDiscount !== null) $promotion->showDiscount = $dto->showDiscount;

        if ($dto->svg !== null) $promotion->svg = $dto->svg;

        return $this->promotionRepository->save($promotion);
    }
}
