<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\DTOs\Feed;

use Spatie\LaravelData\Data;

class FeedInfoData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $company,
        public readonly string $description,
        public readonly string $url,
        public readonly bool $preprice,
    ) {}
}
