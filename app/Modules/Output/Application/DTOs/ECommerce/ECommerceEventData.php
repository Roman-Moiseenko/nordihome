<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\DTOs\ECommerce;

use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

/**
 * DTO входящего события e-commerce (Google Analytics).
 *
 * e_id — идентификатор товара либо список вида [['id' => int, 'quantity' => int], ...].
 */
class ECommerceEventData extends Data
{
    public function __construct(
        #[Required, StringType]
        public readonly string $e_type,
        #[Required]
        public readonly mixed $e_id,
        #[Nullable, Numeric]
        public readonly ?int $quantity = null,
    ) {}
}
