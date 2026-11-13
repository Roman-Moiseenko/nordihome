<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoParameter;

use App\Modules\Marketplace\Domain\Entities\AvitoParameterEntity;
use Spatie\LaravelData\Data;

class AvitoParameterViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $value,
        /** @var string[] */
        public readonly array $variants,
        public readonly bool $isHtml,
    ) {}

    public static function fromEntity(AvitoParameterEntity $entity): self
    {
        return new self(
            id: $entity->id,
            name: $entity->name,
            slug: $entity->slug,
            value: $entity->value,
            variants: $entity->variants,
            isHtml: $entity->isHtml,
        );
    }
}
