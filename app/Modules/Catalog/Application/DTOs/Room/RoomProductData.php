<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Room;

use Spatie\LaravelData\Data;

/**
 * DTO для списка комнат товара (на странице Show/edit товара).
 */
class RoomProductData extends Data
{
    public function __construct(
        public readonly int    $id,
        public readonly string $name,
        public readonly string $slug,
    )
    {
    }
}
