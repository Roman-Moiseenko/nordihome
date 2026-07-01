<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Attribute;

use Spatie\LaravelData\Data;

/**
 * DTO для атрибута на странице категории (сгруппированы в parent/self).
 */
class AttributeCategoryData extends Data
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly string  $group,      // Название группы атрибутов
        public readonly bool    $filter,     // Участвует в фильтре
        public readonly string  $type_text,  // Человекочитаемый тип
        public readonly ?string $image,      // Иконка атрибута
    )
    {
    }
}
