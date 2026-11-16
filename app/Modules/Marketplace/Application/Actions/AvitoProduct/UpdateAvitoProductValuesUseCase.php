<?php

namespace App\Modules\Marketplace\Application\Actions\AvitoProduct;

use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductValuesUpdateData;
use App\Modules\Marketplace\Domain\Entities\AvitoProductValueEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryFieldRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoProductValueRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateAvitoProductValuesUseCase
{
    public function __construct(
        private AvitoProductRepositoryInterface $avitoProductRepository,
        private AvitoProductValueRepositoryInterface $valueRepository,
        private AvitoCategoryFieldRepositoryInterface $fieldRepository,
    ) {}

    public function execute(int $id, AvitoProductValuesUpdateData $dto, UserPermission $permission): void
    {
        if (!$permission->can('marketplace.avito.edit')) {
            throw new AccessDeniedException();
        }

        $product = $this->avitoProductRepository->getById($id);

        if ($product->avitoCategoryId === null || empty($dto->values)) {
            return;
        }

        $fieldMap = [];
        foreach ($this->fieldRepository->getByCategoryId($product->avitoCategoryId) as $field) {
            $fieldMap[$field['id']] = $field;
        }

        $existing = [];
        foreach ($this->valueRepository->getByProductId($product->id) as $value) {
            $existing[$value->avitoCategoryFieldId] = $value;
        }

        foreach ($dto->values as $item) {
            $fieldId = (int) ($item['avito_category_field_id'] ?? $item['field_id'] ?? 0);
            if ($fieldId <= 0) {
                continue;
            }

            $value = $this->normalizeValue($item['value'] ?? null, $fieldMap[$fieldId]['type'] ?? null);

            $entity = $existing[$fieldId] ?? new AvitoProductValueEntity(
                avitoProductId: $product->id,
                avitoCategoryFieldId: $fieldId,
            );

            $entity->value = $value;

            $this->valueRepository->save($entity);
        }
    }

    private function normalizeValue(mixed $value, ?string $type): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($type === 'array') {
            if (is_array($value)) {
                $value = array_values(array_map('strval', $value));
            } else {
                $value = [$value === '' ? '' : (string) $value];
            }

            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        if (is_array($value)) {
            $value = implode(', ', array_map('strval', $value));
        }

        return (string) $value;
    }
}
