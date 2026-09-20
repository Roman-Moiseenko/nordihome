<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\DTOs\Feed;

use Spatie\LaravelData\Data;

class FeedProductData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $description,
        /** @var string[] */
        public readonly array $images,
        public readonly string $url,
        public readonly float $price,
        public readonly float $preprice,
        public readonly ?int $category,
        public readonly bool $store,
        public readonly bool $pickup,
        public readonly bool $delivery,
        public readonly string $code,
    ) {}
}
