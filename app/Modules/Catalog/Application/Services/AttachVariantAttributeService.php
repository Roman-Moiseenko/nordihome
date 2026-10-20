<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Services;

use App\Modules\Catalog\Domain\Entities\AttributeEntity;
use App\Modules\Catalog\Domain\Interfaces\AttributeProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\AttributeRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\AttributeName;

/**
 * Назначает товару вариантный атрибут с указанными вариантами.
 *
 * Если вариант не существует — создаёт его.
 * Если атрибут уже привязан к товару — добавляет новые варианты к существующим.
 */
final readonly class AttachVariantAttributeService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private AttributeRepositoryInterface $attributeRepository,
        private AttributeProductRepositoryInterface $attributeProductRepository,
    ) {
    }

    /**
     * @param int              $productId     ID товара
     * @param array<int, mixed> $values        Список необработанных названий вариантов
     * @param AttributeName     $attributeName Название системного атрибута
     */
    public function execute(int $productId, array $values, AttributeName $attributeName): void
    {
        if (!$this->productRepository->exists($productId)) {
            return;
        }

        $attribute = $this->attributeRepository->findByName((string) $attributeName);
        if ($attribute === null) {
            return;
        }

        $names = $this->uniqueNames($values);
        if (empty($names)) {
            return;
        }

        $attribute = $this->ensureVariants($attribute, $names);

        foreach ($names as $name) {
            $variant = $attribute->findVariantByName($name);
            if ($variant === null || $variant->id === null) {
                continue;
            }

            $this->attributeProductRepository->attachVariant($productId, $attribute->id, $variant->id);
        }
    }

    /**
     * @param array<int, mixed> $values
     * @return string[]
     */
    private function uniqueNames(array $values): array
    {
        $names = [];

        foreach ($values as $value) {
            $name = $this->normalize((string) $value);
            if ($name !== '') {
                $names[$name] = true;
            }
        }

        return array_keys($names);
    }

    /**
     * Приводит название к формату: первая буква заглавная, остальные строчные.
     */
    private function normalize(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        return mb_strtoupper(mb_substr($value, 0, 1, 'UTF-8'), 'UTF-8')
            . mb_strtolower(mb_substr($value, 1, null, 'UTF-8'), 'UTF-8');
    }

    /**
     * Добавляет отсутствующие варианты в сущность атрибута и сохраняет их.
     *
     * @param string[] $names
     */
    private function ensureVariants(AttributeEntity $attribute, array $names): AttributeEntity
    {
        $needSave = false;

        foreach ($names as $name) {
            if ($attribute->findVariantByName($name) === null) {
                $attribute->addVariant($name);
                $needSave = true;
            }
        }

        if (!$needSave) {
            return $attribute;
        }

        return $this->attributeRepository->save($attribute);
    }
}
