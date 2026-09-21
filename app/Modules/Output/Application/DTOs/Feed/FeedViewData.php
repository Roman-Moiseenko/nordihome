<?php

namespace App\Modules\Output\Application\DTOs\Feed;

use App\Modules\Shared\Application\DTOs\ListCodeData;
use App\Modules\Shared\Application\DTOs\ListNameData;
use Spatie\LaravelData\Data;

class FeedViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly bool $active,
        public readonly bool $setPreprice,
        public readonly string $setTitle,
        public readonly string $setDescription,
        public readonly ?int $priceMin,
        public readonly ?int $priceMax,

        /** @var ListCodeData[] $productsIn */
        public readonly array $productsIn,
        /** @var ListCodeData[] $productsOut */
        public readonly array $productsOut,
        /** @var ListNameData[] $tagsIn */
        public readonly array $tagsIn,
        /** @var ListNameData[] $tagsOut */
        public readonly array $tagsOut,
        /** @var int[] */
        public readonly array $categoriesIn,
        /** @var int[] */
        public readonly array $categoriesOut,
        /** @var int[] */
        public readonly array $roomsIn,
        /** @var int[] */
        public readonly array $roomsOut,
        /** @var int[] */
        public readonly array $promotionsIn,
        /** @var int[] */
        public readonly array $promotionsOut,
        /** @var int[] */
        public readonly array $groupsIn,
        /** @var int[] */
        public readonly array $groupsOut,

    ){}
}
