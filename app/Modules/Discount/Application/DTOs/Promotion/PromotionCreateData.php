<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\DTOs\Promotion;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

/**
 * DTO для создания акции. Принимается только название.
 */
class PromotionCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $name,
    )
    {
    }
}
