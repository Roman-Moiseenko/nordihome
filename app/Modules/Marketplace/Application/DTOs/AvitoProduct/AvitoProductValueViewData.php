<?php

namespace App\Modules\Marketplace\Application\DTOs\AvitoProduct;

use App\Modules\Marketplace\Domain\Entities\AvitoProductValueEntity;
use Spatie\LaravelData\Data;

/**
 * Значение параметра категории для вкладки «Параметры» карточки товара Авито.
 */
class AvitoProductValueViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly int $fieldId,
        public readonly string $name,
        public readonly ?string $type,
        public readonly array $allowedValues,
        /** @var string|array<int, string>|null */
        public readonly string|array|null $value,
    ) {}

    /**
     * @param array<string, mixed> $field {id, name, type, allowed_values}
     */
    public static function fromEntity(AvitoProductValueEntity $entity, array $field): self
    {
        $type = $field['type'] ?? null;
        $value = $entity->value;

        // Для массивов значение хранится как JSON.
        if ($type === 'array' && is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $value = $decoded;
            }
        }

        return new self(
            id: $entity->id,
            fieldId: $entity->avitoCategoryFieldId,
            name: $field['name'] ?? (string) $entity->avitoCategoryFieldId,
            type: $type,
            allowedValues: is_array($field['allowed_values'] ?? null) ? $field['allowed_values'] : [],
            value: $value,
        );
    }
}
