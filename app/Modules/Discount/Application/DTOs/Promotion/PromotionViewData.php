<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\DTOs\Promotion;

use App\Modules\Discount\Domain\Entities\PromotionEntity;
use Spatie\LaravelData\Data;

class PromotionViewData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public string $conditionUrl,
        public ?int $discount,
        public ?array $meta,
        public mixed $startAt,
        public mixed $finishAt,
        public bool $menu,
        public bool $showTitle,
        public string $svg,
        public string $colorClass,
        public string $positionClass,
        public string $textTag,
        public bool $showTag,
        public bool $showDiscount,
    )
    {
    }

    public static function fromEntity(PromotionEntity $entity): self
    {
        return new self(
            id: $entity->id ?? 0,
            name: $entity->name,
            slug: (string) $entity->slug,
            conditionUrl: $entity->conditionUrl,
            discount: $entity->discount,
            meta: $entity->meta ? [
                'title' => $entity->meta->getTitle(),
                'description' => $entity->meta->getDescription(),
            ] : null,
            startAt: $entity->startAt?->format('Y-m-d'),
            finishAt: $entity->finishAt?->format('Y-m-d'),
            menu: $entity->menu,
            showTitle: $entity->showTitle,
            svg: $entity->svg ?? '',
            colorClass: $entity->colorClass,
            positionClass: $entity->positionClass,
            textTag: $entity->textTag,
            showTag: $entity->showTag,
            showDiscount: $entity->showDiscount,
        );
    }
}
