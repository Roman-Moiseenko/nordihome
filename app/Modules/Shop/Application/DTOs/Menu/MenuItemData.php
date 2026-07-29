<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\DTOs\Menu;

use Spatie\LaravelData\Data;

class MenuItemData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $url,
        public readonly string $svg,
        public readonly int $sort,
    )
    {
    }
}