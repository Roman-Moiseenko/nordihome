<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\DTOs\Feed;

use Spatie\LaravelData\Data;

class FeedCategoryData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?int $parent = null,
    ) {}
}
