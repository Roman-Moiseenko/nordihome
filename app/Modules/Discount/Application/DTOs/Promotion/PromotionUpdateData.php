<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\DTOs\Promotion;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

/**
 * DTO для обновления акции. Обновляются только переданные (не null) поля.
 */
class PromotionUpdateData extends Data
{
    public function __construct(
        #[Nullable, StringType, Max(255)]
        public readonly ?string $name,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $title,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $slug,
        #[Nullable, StringType]
        public readonly ?string $description,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $conditionUrl,
        #[Nullable, BooleanType]
        public readonly ?bool $menu,
        #[Nullable, BooleanType]
        public readonly ?bool $showTitle,
        #[Nullable, Numeric]
        public readonly ?int $discount,
        #[Nullable, BooleanType]
        public readonly ?bool $published,
        #[Nullable, BooleanType]
        public readonly ?bool $active,
        #[Nullable, StringType]
        public readonly ?string $startAt,
        #[Nullable, StringType]
        public readonly ?string $finishAt,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $colorClass,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $positionClass,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $textTag,
        #[Nullable, BooleanType]
        public readonly ?bool $showTag,
        #[Nullable, BooleanType]
        public readonly ?bool $showDiscount,
        #[Nullable, StringType]
        public readonly ?string $svg,
    )
    {
    }
}
