<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Attribute;

use Spatie\LaravelData\Data;

/**
 * Фильтр списка атрибутов (соответствует фильтрам AttributeRepository::getIndex).
 *
 * Поля приходят из запроса: name, group_id, category_id, _filter, size.
 */
class FilterAttributeIndexData extends Data
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?int $groupId = null,
        public readonly ?int $categoryId = null,
        public readonly ?bool $filter = null,
        public readonly int $perPage = 20,
        public ?int $count = 0,
    ) {}
}
