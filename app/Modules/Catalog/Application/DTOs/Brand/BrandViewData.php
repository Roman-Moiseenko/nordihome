<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Brand;

use App\Modules\Catalog\Domain\Entities\BrandEntity;
use Spatie\LaravelData\Data;

class BrandViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $url,
        public readonly ?string $description,
        public readonly ?int $currencyId,
        public readonly ?string $parserClass,
        /** @var string[] */
        public readonly array $sameAs = [],
    )
    {
    }

    public static function fromEntity(BrandEntity $brand): self
    {
        return new self(
            id: $brand->id,
            name: $brand->name,
            url: $brand->url ?: null,
            description: $brand->description ?: null,
            currencyId: $brand->currencyId,
            parserClass: $brand->parserClass,
            sameAs: $brand->sameAs,
        );
    }
}
