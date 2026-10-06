<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\AttributeGroup;

use Spatie\LaravelData\Data;

/**
 * DTO атрибута в составе группы (только для отображения).
 */
class AttributeGroupAttributeData extends Data
{
    public function __construct(
        public readonly int    $id,
        public readonly string $name,
        public readonly string $type_text,
        public readonly bool   $filter,
    )
    {
    }
}
