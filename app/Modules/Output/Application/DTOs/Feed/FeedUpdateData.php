<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\DTOs\Feed;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class FeedUpdateData extends Data
{
    public function __construct(
        #[Nullable, StringType, Max(255)]
        public readonly ?string $name,
        #[Nullable, BooleanType]
        public readonly ?bool $active,
        /** @var int[]|null */
        #[Nullable, ArrayType]
        public readonly ?array $productsIn,
        /** @var int[]|null */
        #[Nullable, ArrayType]
        public readonly ?array $productsOut,
        /** @var int[]|null */
        #[Nullable, ArrayType]
        public readonly ?array $categoriesIn,
        /** @var int[]|null */
        #[Nullable, ArrayType]
        public readonly ?array $categoriesOut,
        /** @var int[]|null */
        #[Nullable, ArrayType]
        public readonly ?array $roomsIn,
        /** @var int[]|null */
        #[Nullable, ArrayType]
        public readonly ?array $roomsOut,
        /** @var int[]|null */
        #[Nullable, ArrayType]
        public readonly ?array $promotionsIn,
        /** @var int[]|null */
        #[Nullable, ArrayType]
        public readonly ?array $promotionsOut,
        /** @var int[]|null */
        #[Nullable, ArrayType]
        public readonly ?array $groupsIn,
        /** @var int[]|null */
        #[Nullable, ArrayType]
        public readonly ?array $groupsOut,
        /** @var int[]|null */
        #[Nullable, ArrayType]
        public readonly ?array $tagsIn,
        /** @var int[]|null */
        #[Nullable, ArrayType]
        public readonly ?array $tagsOut,
        #[Nullable, BooleanType]
        public readonly ?bool $setPreprice,
        #[Nullable, StringType]
        public readonly ?string $setTitle,
        #[Nullable, StringType]
        public readonly ?string $setDescription,
    ) {}
}
