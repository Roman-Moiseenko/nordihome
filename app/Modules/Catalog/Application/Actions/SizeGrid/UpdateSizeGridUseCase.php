<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\SizeGrid;

use App\Modules\Catalog\Application\DTOs\SizeGrid\SizeGridUpdateData;
use App\Modules\Catalog\Domain\Entities\SizeGridEntity;
use App\Modules\Catalog\Domain\Entities\SizeGridValueEntity;
use App\Modules\Catalog\Domain\Interfaces\ProductSizeRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\SizeGridRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\SizeGridDimension;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateSizeGridUseCase
{
    public function __construct(
        private SizeGridRepositoryInterface $sizeGridRepository,
        private ProductSizeRepositoryInterface $productSizeRepository,
    ) {
    }

    public function execute(int $id, SizeGridUpdateData $dto, UserPermission $userPermission): SizeGridEntity
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        $grid = $this->sizeGridRepository->getById($id);

        $grid->name = trim($dto->name);
        $grid->dimensions = $this->buildDimensions($dto->dimensions);
        $grid->values = $this->buildValues($dto->values);

        $saved = $this->sizeGridRepository->save($grid);

        // Перестраиваем размерный ряд всех товаров, у которых настроена эта сетка.
        // Удалённые варианты автоматически обнуляют связанные записи stock.
        foreach ($this->productSizeRepository->getProductIdsBySizeGridId($saved->id) as $productId) {
            $this->productSizeRepository->rebuildForProduct($productId, $saved, []);
        }

        return $saved;
    }

    /**
     * @param array<int, string|array<string, mixed>> $dimensions
     * @return SizeGridDimension[]
     */
    private function buildDimensions(array $dimensions): array
    {
        $result = [];

        foreach ($dimensions as $dimension) {
            $vo = $this->resolveDimension($dimension);

            if ($vo !== null) {
                $result[$vo->code] = $vo;
            }
        }

        return array_values($result);
    }

    private function resolveDimension(string|array $dimension): ?SizeGridDimension
    {
        if (is_string($dimension)) {
            return SizeGridDimension::findByCode($dimension);
        }

        $code = (string) ($dimension['code'] ?? '');
        if ($code === '') {
            return null;
        }

        $known = SizeGridDimension::findByCode($code);

        return $known ?? new SizeGridDimension(
            code: $code,
            name: (string) ($dimension['name'] ?? $code),
        );
    }

    /**
     * @param array<int, array<string, mixed>> $values
     * @return SizeGridValueEntity[]
     */
    private function buildValues(array $values): array
    {
        $result = [];

        foreach (array_values($values) as $index => $value) {
            if (!is_array($value)) {
                continue;
            }

            $dimensionCode = (string) ($value['dimensionCode'] ?? $value['dimension_code'] ?? '');
            $valueString = trim((string) ($value['value'] ?? ''));

            if ($dimensionCode === '' || $valueString === '') {
                continue;
            }

            $label = $value['label'] ?? null;
            $label = ($label === null || trim((string) $label) === '') ? null : (string) $label;

            $entity = new SizeGridValueEntity(
                dimensionCode: $dimensionCode,
                value: $valueString,
                label: $label,
                sortOrder: (int) ($value['sortOrder'] ?? $value['sort_order'] ?? $index),
                isActive: (bool) ($value['isActive'] ?? $value['is_active'] ?? true),
            );

            $id = $value['id'] ?? null;
            if ($id !== null) {
                $entity->id = (int) $id;
            }

            $result[] = $entity;
        }

        return $result;
    }
}
