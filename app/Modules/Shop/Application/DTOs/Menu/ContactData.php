<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\DTOs\Menu;

use Spatie\LaravelData\Data;

class ContactData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $icon,
        public readonly string $color,
        public readonly string $url,
        public readonly int $type,
        public readonly string $slug,
        public readonly string $svg,
    )
    {
    }
}
