<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoParameterRegistry;

use App\Modules\Marketplace\Domain\Entities\AvitoParameterRegistryEntity;
use Spatie\LaravelData\Data;

class AvitoParameterRegistryViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $nameEn,
        public readonly ?string $nameRu,
        public readonly ?string $kind,
        public readonly ?string $field,
        public readonly ?string $type,
        public readonly bool $isRequired,
    ) {}

    public static function fromEntity(AvitoParameterRegistryEntity $entity): self
    {
        return new self(
            id: $entity->id,
            nameEn: $entity->nameEn,
            nameRu: $entity->nameRu,
            kind: $entity->kind,
            field: $entity->field,
            type: $entity->type,
            isRequired: $entity->isRequired,
        );
    }
}
