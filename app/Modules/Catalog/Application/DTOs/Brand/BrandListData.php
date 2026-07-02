<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Brand;

use App\Modules\Catalog\Domain\Entities\BrandEntity;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class BrandListData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $parser = null,
    )
    {
    }

    public static function fromEntity(BrandEntity $entity): self
    {
        return new self(
            id: $entity->id,
            name: $entity->name,
            parser: $entity->parserClass,
        );
    }

    /**
     * @param BrandEntity[] $entities
     * @return self[]
     */
    public static function fromEntityArray(array $entities): array
    {
        return array_map(fn(BrandEntity $entity) => self::fromEntity($entity), $entities);
    }
}
