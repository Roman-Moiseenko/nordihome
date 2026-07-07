<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\DTOs\ParserLog;

use App\Modules\Parser\Domain\Entities\ParserLogItemEntity;
use Spatie\LaravelData\Data;

class ParserLogItemData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $productId,
        public readonly ?string $code,
        /** @var string[] */
        public readonly array $categoryParser,
        public readonly ?string $priceOld,
        public readonly ?string $priceNew,
    ) {}

    public static function fromEntity(ParserLogItemEntity $item): self
    {
        return new self(
            id: $item->id,
            productId: $item->productId,
            code: $item->code,
            categoryParser: $item->categoryParser,
            priceOld: $item->priceOld,
            priceNew: $item->priceNew,
        );
    }
}
